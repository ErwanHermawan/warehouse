export interface GetCustomerParams {
	customer_type?: string;
	skip?: number;
	take?: number;
	page?: number;
	limit?: number;
	keyword?: string;
	status?: number;
}

export interface VoucherSummary {
	id: number;
	code: string | null;
	issued_at: Date | null;
	expires_at: Date;
	metadata: any | null;
	voucher: {
		id: number;
		name: string | null;
		code: string | null;
		discount_value: number | null;
		voucher_type: string | null;
	} | null;
}

export interface CustomerResponse {
	id: number;
	name: string | null;
	phone: string | null;
	email: string | null;
	customer_type: string | null;
	created_at: Date;
	addresses?: any[];
	auth?: any;
	dealerInformation?: any;
	pointTransactions?: any[];
	total_voucher: number;
	voucher_expiration_date: VoucherSummary | null;
	total_points: number;
	total_order: number;
	total_spending: number;
}
