import React, { useState } from 'react';
import { Database, Lock, Mail, ArrowRight, ShieldAlert } from 'lucide-react';
import { adminApi, AdminUser } from '@/lib/admin-api';

interface AdminLoginProps {
    defaultCredentials: {
        email: string;
        password: string;
        name: string;
    };
    onSuccess: (user: AdminUser) => void;
}

export const AdminLogin: React.FC<AdminLoginProps> = ({
    defaultCredentials,
    onSuccess,
}) => {
    const [email, setEmail] = useState(defaultCredentials.email || '');
    const [password, setPassword] = useState(defaultCredentials.password || '');
    const [isLoading, setIsLoading] = useState(false);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setIsLoading(true);
        setErrorMessage(null);

        try {
            const res = await adminApi.login(email, password);
            if (res.success && res.data) {
                onSuccess(res.data.admin);
            } else {
                setErrorMessage(res.message || 'Authentication failed. Please check credentials.');
            }
        } catch {
            setErrorMessage('Network or server error occurred during login.');
        } finally {
            setIsLoading(false);
        }
    };

    const handleAutoFill = () => {
        setEmail(defaultCredentials.email);
        setPassword(defaultCredentials.password);
    };

    return (
        <div className="flex min-h-screen items-center justify-center bg-neutral-50 px-4 py-12 dark:bg-neutral-950">
            <div className="w-full max-w-md rounded-md border border-neutral-200 bg-white p-8 shadow-xs dark:border-neutral-800 dark:bg-neutral-900">
                {/* Logo & Header */}
                <div className="mb-6 text-center">
                    <div className="mx-auto mb-3 flex size-10 items-center justify-center rounded-sm bg-black text-white dark:bg-white dark:text-black">
                        <Database className="size-5" />
                    </div>
                    <h2 className="text-xl font-semibold tracking-tight text-neutral-950 dark:text-neutral-50">
                        Specimen Repository
                    </h2>
                    <p className="mt-1 text-xs text-neutral-500 dark:text-neutral-400 font-mono uppercase tracking-wider">
                        Administrative Console Login
                    </p>
                </div>

                {errorMessage && (
                    <div className="mb-4 flex items-start gap-2.5 rounded-sm border border-neutral-400 bg-neutral-100 p-3 text-xs text-neutral-900 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200">
                        <ShieldAlert className="size-4 shrink-0 mt-0.5 text-neutral-700 dark:text-neutral-300" />
                        <span>{errorMessage}</span>
                    </div>
                )}

                <form onSubmit={handleSubmit} className="space-y-4">
                    <div>
                        <label className="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                            Administrator Email
                        </label>
                        <div className="relative">
                            <Mail className="pointer-events-none absolute left-3 top-2.5 size-4 text-neutral-400" />
                            <input
                                type="email"
                                required
                                value={email}
                                onChange={(e) => setEmail(e.target.value)}
                                placeholder="name@domain.com"
                                className="w-full rounded-sm border border-neutral-300 bg-transparent py-2 pl-9 pr-3 text-sm text-neutral-900 placeholder:text-neutral-400 focus:border-black focus:outline-hidden dark:border-neutral-700 dark:text-neutral-100 dark:focus:border-white"
                            />
                        </div>
                    </div>

                    <div>
                        <label className="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                            Password
                        </label>
                        <div className="relative">
                            <Lock className="pointer-events-none absolute left-3 top-2.5 size-4 text-neutral-400" />
                            <input
                                type="password"
                                required
                                value={password}
                                onChange={(e) => setPassword(e.target.value)}
                                placeholder="••••••••"
                                className="w-full rounded-sm border border-neutral-300 bg-transparent py-2 pl-9 pr-3 text-sm text-neutral-900 placeholder:text-neutral-400 focus:border-black focus:outline-hidden dark:border-neutral-700 dark:text-neutral-100 dark:focus:border-white"
                            />
                        </div>
                    </div>

                    <button
                        type="submit"
                        disabled={isLoading}
                        className="flex w-full items-center justify-center gap-2 rounded-sm bg-black py-2.5 text-xs font-medium text-white transition-opacity hover:opacity-90 disabled:opacity-50 dark:bg-white dark:text-black"
                    >
                        <span>{isLoading ? 'Authenticating...' : 'Sign In to Dashboard'}</span>
                        {!isLoading && <ArrowRight className="size-3.5" />}
                    </button>
                </form>

                {/* Auto-fill Helper */}
                <div className="mt-6 border-t border-neutral-200 pt-4 dark:border-neutral-800">
                    <p className="text-[11px] text-neutral-500 dark:text-neutral-400 mb-2 text-center">
                        Authorized Default Super Administrator:
                    </p>
                    <button
                        type="button"
                        onClick={handleAutoFill}
                        className="w-full rounded-sm border border-dashed border-neutral-300 py-1.5 text-xs font-mono text-neutral-700 transition-colors hover:bg-neutral-100 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800"
                    >
                        {defaultCredentials.email} ({defaultCredentials.name})
                    </button>
                </div>
            </div>
        </div>
    );
};
