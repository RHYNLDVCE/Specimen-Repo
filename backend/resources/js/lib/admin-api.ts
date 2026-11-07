/**
 * Specimen Repository Admin API Client
 * Interfaces directly with the Laravel + MongoDB REST API (/api/v1/*).
 */

const TOKEN_KEY = 'specimen_admin_token';
const USER_KEY = 'specimen_admin_user';

export interface AdminUser {
    id: string;
    name: string;
    email: string;
    role: string;
    status: string;
    created_at?: string;
    updated_at?: string;
}

export interface ApiResponse<T = unknown> {
    success: boolean;
    message: string;
    data: T;
    meta?: {
        current_page: number;
        per_page: number;
        total: number;
        last_page: number;
    };
    errors?: Record<string, string[]>;
}

class AdminApiClient {
    private baseUrl = '/api/v1';

    public getToken(): string | null {
        if (typeof window === 'undefined') return null;
        return localStorage.getItem(TOKEN_KEY);
    }

    public getUser(): AdminUser | null {
        if (typeof window === 'undefined') return null;
        const userJson = localStorage.getItem(USER_KEY);
        if (!userJson) return null;
        try {
            return JSON.parse(userJson);
        } catch {
            return null;
        }
    }

    public setAuth(user: AdminUser, token: string): void {
        if (typeof window === 'undefined') return;
        localStorage.setItem(TOKEN_KEY, token);
        localStorage.setItem(USER_KEY, JSON.stringify(user));
    }

    public clearAuth(): void {
        if (typeof window === 'undefined') return;
        localStorage.removeItem(TOKEN_KEY);
        localStorage.removeItem(USER_KEY);
    }

    private async request<T = unknown>(
        endpoint: string,
        options: RequestInit = {}
    ): Promise<ApiResponse<T>> {
        const token = this.getToken();
        const headers: Record<string, string> = {
            'Accept': 'application/json',
            ...(options.headers as Record<string, string> || {}),
        };

        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }

        if (!(options.body instanceof FormData) && !headers['Content-Type']) {
            headers['Content-Type'] = 'application/json';
        }

        const url = `${this.baseUrl}${endpoint.startsWith('/') ? endpoint : `/${endpoint}`}`;
        const res = await fetch(url, {
            ...options,
            headers,
        });

        const data = await res.json().catch(() => ({
            success: false,
            message: `Request failed with status ${res.status}`,
        }));

        if (!res.ok && res.status === 401) {
            this.clearAuth();
        }

        return data as ApiResponse<T>;
    }

    // --- Authentication ---
    public async login(email: string, password: string) {
        const res = await this.request<{ admin: AdminUser; token: string }>('/auth/login', {
            method: 'POST',
            body: JSON.stringify({ email, password }),
        });

        if (res.success && res.data) {
            this.setAuth(res.data.admin, res.data.token);
        }

        return res;
    }

    public async me() {
        return this.request<AdminUser>('/auth/me');
    }

    public async logout() {
        const res = await this.request('/auth/logout', { method: 'POST' });
        this.clearAuth();
        return res;
    }

    // --- Reports / Analytics ---
    public async getDashboardMetrics() {
        return this.request<{
            specimens: { total: number; by_status: Record<string, number> };
            loans: {
                total: number;
                active: number;
                pending: number;
                returned: number;
                overdue: number;
                by_status: Record<string, number>;
            };
            totals: {
                taxa: number;
                collections: number;
                researchers: number;
                projects: number;
                admins: number;
            };
        }>('/reports/dashboard');
    }

    public async getSpecimensReport() {
        return this.request<{
            by_status: Array<{ id: string; count: number }>;
            by_kingdom: Array<{ id: string; count: number }>;
            by_preservation_method: Array<{ id: string; count: number }>;
            monthly_acquisitions: Array<{ id: string; count: number }>;
        }>('/reports/specimens');
    }

    public async getLoansReport() {
        return this.request<{
            by_status: Array<{ id: string; count: number }>;
            top_borrowers: Array<{ id: string; count: number }>;
            overdue_count: number;
            overdue_loans: Array<{
                id: string;
                borrower_name: string;
                specimen_code: string;
                loan_date: string;
                expected_return_date: string;
                days_overdue: number;
            }>;
        }>('/reports/loans');
    }

    // --- Specimens ---
    public async getSpecimens(params: Record<string, string | number> = {}) {
        const query = new URLSearchParams(params as Record<string, string>).toString();
        return this.request<Array<any>>(`/specimens${query ? `?${query}` : ''}`);
    }

    public async getSpecimen(id: string) {
        return this.request<any>(`/specimens/${id}`);
    }

    public async createSpecimen(data: any) {
        return this.request<any>('/specimens', {
            method: 'POST',
            body: JSON.stringify(data),
        });
    }

    public async updateSpecimen(id: string, data: any) {
        return this.request<any>(`/specimens/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data),
        });
    }

    public async deleteSpecimen(id: string) {
        return this.request<any>(`/specimens/${id}`, {
            method: 'DELETE',
        });
    }

    // --- Loans ---
    public async getLoans(params: Record<string, string | number> = {}) {
        const query = new URLSearchParams(params as Record<string, string>).toString();
        return this.request<Array<any>>(`/loans${query ? `?${query}` : ''}`);
    }

    public async getLoan(id: string) {
        return this.request<any>(`/loans/${id}`);
    }

    public async createLoan(data: any) {
        return this.request<any>('/loans', {
            method: 'POST',
            body: JSON.stringify(data),
        });
    }

    public async approveLoan(id: string) {
        return this.request<any>(`/loans/${id}/approve`, {
            method: 'POST',
        });
    }

    public async returnLoan(id: string, notes?: string) {
        return this.request<any>(`/loans/${id}/return`, {
            method: 'POST',
            body: JSON.stringify({ notes }),
        });
    }

    public async cancelLoan(id: string) {
        return this.request<any>(`/loans/${id}/cancel`, {
            method: 'POST',
        });
    }

    // --- Taxa ---
    public async getTaxa(params: Record<string, string | number> = {}) {
        const query = new URLSearchParams(params as Record<string, string>).toString();
        return this.request<Array<any>>(`/taxa${query ? `?${query}` : ''}`);
    }

    public async createTaxon(data: any) {
        return this.request<any>('/taxa', {
            method: 'POST',
            body: JSON.stringify(data),
        });
    }

    public async updateTaxon(id: string, data: any) {
        return this.request<any>(`/taxa/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data),
        });
    }

    // --- Collections ---
    public async getCollections(params: Record<string, string | number> = {}) {
        const query = new URLSearchParams(params as Record<string, string>).toString();
        return this.request<Array<any>>(`/collections${query ? `?${query}` : ''}`);
    }

    public async createCollection(data: any) {
        return this.request<any>('/collections', {
            method: 'POST',
            body: JSON.stringify(data),
        });
    }

    // --- Locations ---
    public async getLocations(params: Record<string, string | number> = {}) {
        const query = new URLSearchParams(params as Record<string, string>).toString();
        return this.request<Array<any>>(`/locations${query ? `?${query}` : ''}`);
    }

    public async createLocation(data: any) {
        return this.request<any>('/locations', {
            method: 'POST',
            body: JSON.stringify(data),
        });
    }

    // --- Researchers ---
    public async getResearchers(params: Record<string, string | number> = {}) {
        const query = new URLSearchParams(params as Record<string, string>).toString();
        return this.request<Array<any>>(`/researchers${query ? `?${query}` : ''}`);
    }

    public async createResearcher(data: any) {
        return this.request<any>('/researchers', {
            method: 'POST',
            body: JSON.stringify(data),
        });
    }

    // --- Audit Logs ---
    public async getAuditLogs(params: Record<string, string | number> = {}) {
        const query = new URLSearchParams(params as Record<string, string>).toString();
        return this.request<Array<any>>(`/audit-logs${query ? `?${query}` : ''}`);
    }
}

export const adminApi = new AdminApiClient();
