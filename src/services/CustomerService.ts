import prismaClient from "../lib/prisma";
import { toCustomerResponse } from "../utils/customerMapper";
import { CustomerResponse, GetCustomerParams } from "../types/customer";

export class CustomerService {
	static async get(params: GetCustomerParams = {}): Promise<{
		data: CustomerResponse[];
		total: number;
		page: number;
		limit: number;
	}> {
		const {
			customer_type,
			skip,
			take = 10,
			page = 1,
			limit = 10,
			keyword,
			status,
		} = params;

		const conditions: any[] = [];

		// --- Keyword search (OR across name/phone/email) ---
		if (keyword) {
			conditions.push({
				OR: [
					{ name: { contains: keyword } },
					{ phone: { contains: keyword } },
					{ email: { contains: keyword } },
				],
			});
		}

		// --- Customer type filter ---
		if (customer_type) {
			conditions.push({ customer_type });
		}

		// --- Dealer‑specific status filter ---
		if (customer_type === "DEALER" && status !== undefined) {
			if (status === 0) {
				// pending
				conditions.push({
					OR: [
						{ dealerInformation: null },
						{
							dealerInformation: {
								is: {
									is_ktp_valid: false,
									is_npwp_valid: false,
								},
							},
						},
					],
				});
			} else {
				// status === 1 (valid)
				conditions.push({
					dealerInformation: {
						is: {
							is_ktp_valid: true,
							is_npwp_valid: true,
						},
					},
				});
			}
		}

		// Build the final where clause – if no conditions, return everything
		const where = conditions.length ? { AND: conditions } : {};

		// Fetch customers page (do NOT include VoucherTransaction here to avoid N+1 and extra payload)
		const [customers, total] = await Promise.all([
			prismaClient.customer.findMany({
				where,
				skip,
				take,
				orderBy: { created_at: "desc" },
				include: {
					addresses: true,
					auth: true,
					dealerInformation: true,
					pointTransactions: true,
					// DO NOT include VoucherTransaction here — we will fetch vouchers in a single batched query for dealers only
				},
			}),
			prismaClient.customer.count({ where }),
		]);

		if (!customers || customers.length === 0) {
			return {
				data: [],
				total,
				page,
				limit,
			};
		}

		// Collect all customer IDs for batched queries
		const allCustomerIds = customers
			.map((c: any) => c.id)
			.filter(Boolean) as number[];

		// Determine which returned customers are DEALERs and collect their ids
		const dealerCustomerIds = customers
			.filter((c: any) => c.customer_type === "DEALER")
			.map((c: any) => c.id)
			.filter(Boolean) as number[];

		const now = new Date();

		// Prepare maps for voucher counts and next-expiring voucher per customer
		const activeCountMap = new Map<number, number>();
		const nextExpiringMap = new Map<number, any>();

		// Prepare maps for order stats per customer
		const orderCountMap = new Map<number, number>();
		const orderSpendingMap = new Map<number, number>();

		// Run voucher fetch (dealers only) and order stats (all customers) in parallel
		const [activeVouchers, orderStats] = await Promise.all([
			dealerCustomerIds.length > 0
				? prismaClient.voucherTransaction.findMany({
						where: {
							customer_id: { in: dealerCustomerIds },
							status: "ACTIVE",
							OR: [{ expires_at: null }, { expires_at: { gt: now } }],
						},
						select: {
							id: true,
							customer_id: true,
							code: true,
							issued_at: true,
							expires_at: true,
							metadata: true,
							voucher: {
								select: {
									id: true,
									name: true,
									code: true,
									discount_value: true,
									voucher_type: true,
								},
							},
						},
						orderBy: { issued_at: "desc" },
					})
				: Promise.resolve([]),

			// Fetch order count and total spending per customer in a single grouped query
			prismaClient.order.groupBy({
				by: ["customer_id"],
				where: { customer_id: { in: allCustomerIds } },
				_count: { id: true },
				_sum: { total_price: true },
			}),
		]);

		// Build voucher count and next-expiring maps
		for (const vt of activeVouchers) {
			const cid = Number(vt.customer_id);
			activeCountMap.set(cid, (activeCountMap.get(cid) ?? 0) + 1);
		}

		const expiringCandidates = activeVouchers.filter(
			(vt) => vt.expires_at && new Date(vt.expires_at) > now
		);

		for (const vt of expiringCandidates) {
			const cid = Number(vt.customer_id);
			const vtExpires = new Date(vt.expires_at as Date);
			const existing = nextExpiringMap.get(cid);
			if (!existing || vtExpires.getTime() < existing.expires_at.getTime()) {
				nextExpiringMap.set(cid, {
					id: vt.id,
					code: vt.code,
					issued_at: vt.issued_at ?? null,
					expires_at: vtExpires,
					metadata: vt.metadata ?? null,
					voucher: vt.voucher
						? {
								id: vt.voucher.id,
								name: vt.voucher.name ?? null,
								code: vt.voucher.code ?? null,
								discount_value: (vt.voucher as any).discount_value ?? null,
								voucher_type: (vt.voucher as any).voucher_type ?? null,
							}
						: null,
				});
			}
		}

		// Build order stats maps
		for (const row of orderStats) {
			const cid = Number(row.customer_id);
			orderCountMap.set(cid, row._count.id);
			orderSpendingMap.set(cid, Number(row._sum.total_price ?? 0));
		}

		// Map customers to response and attach voucher summary fields and order stats
		const mapped = customers.map((c: any) => {
			const base = toCustomerResponse(c as any);
			const cid = Number(c.id);

			// Compute total points from already-loaded pointTransactions
			const totalPoints = (c.pointTransactions ?? []).reduce(
				(sum: number, pt: any) => sum + (Number(pt.points) || 0),
				0
			);

			// Only dealers will have counts in the voucher maps (others default to 0 / null)
			const voucherActiveCount = activeCountMap.get(cid) ?? 0;
			const nextExp = nextExpiringMap.get(cid) ?? null;

			return {
				...base,
				total_voucher: voucherActiveCount,
				voucher_expiration_date: nextExp,
				total_points: totalPoints,
				total_order: orderCountMap.get(cid) ?? 0,
				total_spending: orderSpendingMap.get(cid) ?? 0,
			} as CustomerResponse;
		});

		return {
			data: mapped,
			total,
			page,
			limit,
		};
	}
}
