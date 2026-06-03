import { CustomerResponse } from "../types/customer";

export function toCustomerResponse(customer: any): Omit<
	CustomerResponse,
	"total_voucher" | "voucher_expiration_date" | "total_points" | "total_order" | "total_spending"
> {
	return {
		id: customer.id,
		name: customer.name ?? null,
		phone: customer.phone ?? null,
		email: customer.email ?? null,
		customer_type: customer.customer_type ?? null,
		created_at: customer.created_at,
		addresses: customer.addresses ?? [],
		auth: customer.auth ?? null,
		dealerInformation: customer.dealerInformation ?? null,
		pointTransactions: customer.pointTransactions ?? [],
	};
}
