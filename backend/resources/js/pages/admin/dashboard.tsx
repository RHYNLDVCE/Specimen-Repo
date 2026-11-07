import React, { useState, useEffect, useTransition } from 'react';
import { Head } from '@inertiajs/react';
import { 
    Layers, 
    GitPullRequest, 
    BookOpen, 
    Boxes, 
    Compass, 
    Users, 
    ShieldCheck, 
    Plus, 
    Search, 
    Eye, 
    Edit3, 
    Trash2, 
    CheckCircle2, 
    RotateCcw, 
    XCircle, 
    AlertTriangle, 
    RefreshCw, 
    Filter, 
    ArrowUpRight,
    Building2,
    Calendar,
    Activity,
    Database,
    PanelRightOpen,
    PanelRightClose,
    PanelLeftOpen,
    PanelLeftClose
} from 'lucide-react';
import { adminApi, AdminUser } from '@/lib/admin-api';
import { AdminHeader } from '@/components/admin/admin-header';
import { AdminSidebar } from '@/components/admin/admin-sidebar';
import { AdminSidePanel } from '@/components/admin/admin-side-panel';
import { AdminLogin } from '@/components/admin/admin-login';
import { SpecimenModal } from '@/components/admin/modals/specimen-modal';
import { SpecimenDetailModal } from '@/components/admin/modals/specimen-detail-modal';
import { LoanModal } from '@/components/admin/modals/loan-modal';
import { TaxonModal, CollectionModal, LocationModal, ResearcherModal } from '@/components/admin/modals/entity-modals';

interface Props {
    initialTab?: string;
    initialMetrics?: any;
    defaultCredentials: {
        email: string;
        password: string;
        name: string;
    };
}

export default function AdminDashboard({
    initialTab = 'overview',
    initialMetrics,
    defaultCredentials,
}: Props) {
    const [currentTab, setCurrentTab] = useState(initialTab);
    const [user, setUser] = useState<AdminUser | null>(null);
    const [isInitialized, setIsInitialized] = useState(false);
    const [isDark, setIsDark] = useState(false);
    const [, startTransition] = useTransition();

    // Data States
    const [metrics, setMetrics] = useState<any>(initialMetrics || null);
    const [specimens, setSpecimens] = useState<any[]>([]);
    const [specimenTotal, setSpecimenTotal] = useState(0);
    const [specimenPage, setSpecimenPage] = useState(1);
    const [specimenSearch, setSpecimenSearch] = useState('');
    const [specimenStatusFilter, setSpecimenStatusFilter] = useState('');
    const [specimenPresFilter, setSpecimenPresFilter] = useState('');

    const [loans, setLoans] = useState<any[]>([]);
    const [loanStatusFilter, setLoanStatusFilter] = useState('');
    const [loanSearch, setLoanSearch] = useState('');

    const [taxa, setTaxa] = useState<any[]>([]);
    const [collections, setCollections] = useState<any[]>([]);
    const [locations, setLocations] = useState<any[]>([]);
    const [researchers, setResearchers] = useState<any[]>([]);
    const [auditLogs, setAuditLogs] = useState<any[]>([]);

    const [isLoading, setIsLoading] = useState(false);
    const [toastMessage, setToastMessage] = useState<{ text: string; isError?: boolean } | null>(null);

    // Modals
    const [isSpecimenModalOpen, setIsSpecimenModalOpen] = useState(false);
    const [specimenToEdit, setSpecimenToEdit] = useState<any | null>(null);

    const [isDetailModalOpen, setIsDetailModalOpen] = useState(false);
    const [detailSpecimen, setDetailSpecimen] = useState<any | null>(null);

    const [isLoanModalOpen, setIsLoanModalOpen] = useState(false);
    const [loanPreSelectedSpecimen, setLoanPreSelectedSpecimen] = useState<any | null>(null);

    const [isTaxonModalOpen, setIsTaxonModalOpen] = useState(false);
    const [isCollectionModalOpen, setIsCollectionModalOpen] = useState(false);
    const [isLocationModalOpen, setIsLocationModalOpen] = useState(false);
    const [isResearcherModalOpen, setIsResearcherModalOpen] = useState(false);

    // Side Panel States
    const [isSidebarCollapsed, setIsSidebarCollapsed] = useState(false);
    const [isMobileSidebarOpen, setIsMobileSidebarOpen] = useState(false);
    const [isSidePanelOpen, setIsSidePanelOpen] = useState(true);
    const [selectedSidePanelSpecimen, setSelectedSidePanelSpecimen] = useState<any | null>(null);

    // 1. Initial Authentication Check
    useEffect(() => {
        const savedUser = adminApi.getUser();
        const token = adminApi.getToken();

        // Check dark mode
        const isDarkMode = document.documentElement.classList.contains('dark') || 
            window.matchMedia('(prefers-color-scheme: dark)').matches;
        setIsDark(isDarkMode);

        if (savedUser && token) {
            setUser(savedUser);
            adminApi.me().then((res) => {
                if (res.success && res.data) {
                    setUser(res.data);
                }
            });
        }
        setIsInitialized(true);
    }, []);

    // Show temporary toast notification
    const showToast = (text: string, isError = false) => {
        setToastMessage({ text, isError });
        setTimeout(() => setToastMessage(null), 4000);
    };

    // Dark mode toggle
    const toggleDarkMode = () => {
        const next = !isDark;
        setIsDark(next);
        if (next) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    };

    // 2. Fetch Active Tab Data
    const loadData = async () => {
        if (!user) return;
        setIsLoading(true);

        try {
            // Always refresh metrics & lookups
            const [mRes, tRes, cRes, lRes, rRes] = await Promise.all([
                adminApi.getDashboardMetrics(),
                adminApi.getTaxa({ per_page: 100 }),
                adminApi.getCollections({ per_page: 50 }),
                adminApi.getLocations({ per_page: 50 }),
                adminApi.getResearchers({ per_page: 50 }),
            ]);

            if (mRes.success) setMetrics(mRes.data);
            if (tRes.success) setTaxa(tRes.data || []);
            if (cRes.success) setCollections(cRes.data || []);
            if (lRes.success) setLocations(lRes.data || []);
            if (rRes.success) setResearchers(rRes.data || []);

            // Specific tab queries
            if (currentTab === 'specimens' || currentTab === 'overview') {
                const spRes = await adminApi.getSpecimens({
                    page: specimenPage,
                    per_page: 15,
                    search: specimenSearch,
                    status: specimenStatusFilter,
                    preservation_method: specimenPresFilter,
                });
                if (spRes.success) {
                    setSpecimens(spRes.data || []);
                    setSpecimenTotal(spRes.meta?.total || 0);
                }
            }

            if (currentTab === 'loans' || currentTab === 'overview') {
                const lnRes = await adminApi.getLoans({
                    per_page: 50,
                    status: loanStatusFilter,
                    search: loanSearch,
                });
                if (lnRes.success) {
                    setLoans(lnRes.data || []);
                }
            }

            if (currentTab === 'audit-logs') {
                const auRes = await adminApi.getAuditLogs({ per_page: 30 });
                if (auRes.success) {
                    setAuditLogs(auRes.data || []);
                }
            }
        } catch {
            showToast('Failed to refresh repository data.', true);
        } finally {
            setIsLoading(false);
        }
    };

    useEffect(() => {
        if (user) {
            loadData();
        }
    }, [user, currentTab, specimenPage, specimenStatusFilter, specimenPresFilter, loanStatusFilter]);

    // Handle tab change
    const handleTabChange = (tab: string) => {
        startTransition(() => {
            setCurrentTab(tab);
            window.history.replaceState(null, '', `/admin/${tab === 'overview' ? '' : tab}`);
        });
    };

    // Logout
    const handleLogout = async () => {
        await adminApi.logout();
        setUser(null);
        showToast('Signed out of administrative console.');
    };

    // Loan Workflow Actions
    const handleApproveLoan = async (id: string) => {
        const res = await adminApi.approveLoan(id);
        if (res.success) {
            showToast('Loan approved. Specimen status marked as LOANED.');
            loadData();
        } else {
            showToast(res.message || 'Loan approval failed.', true);
        }
    };

    const handleReturnLoan = async (id: string) => {
        const notes = prompt('Enter return inspection notes (optional):', 'Specimen returned intact and in good condition.') || undefined;
        const res = await adminApi.returnLoan(id, notes);
        if (res.success) {
            showToast('Specimen return recorded. Specimen restored to ACTIVE.');
            loadData();
        } else {
            showToast(res.message || 'Loan return failed.', true);
        }
    };

    const handleCancelLoan = async (id: string) => {
        if (!confirm('Are you sure you want to cancel this loan request?')) return;
        const res = await adminApi.cancelLoan(id);
        if (res.success) {
            showToast('Loan request cancelled.');
            loadData();
        } else {
            showToast(res.message || 'Cancellation failed.', true);
        }
    };

    // Delete Specimen
    const handleDeleteSpecimen = async (sp: any) => {
        if (!confirm(`Are you sure you want to deaccession specimen ${sp.specimen_code} (${sp.scientific_name})?`)) return;
        const res = await adminApi.deleteSpecimen(sp.id);
        if (res.success) {
            showToast(`Specimen ${sp.specimen_code} removed from repository.`);
            if (isDetailModalOpen) setIsDetailModalOpen(false);
            loadData();
        } else {
            showToast(res.message || 'Deletion failed. Check loan restrictions.', true);
        }
    };

    if (!isInitialized) return null;

    // If not authenticated, render the minimalist login view
    if (!user) {
        return (
            <>
                <Head title="Admin Login — Specimen Repository" />
                <AdminLogin
                    defaultCredentials={defaultCredentials}
                    onSuccess={(loggedUser) => {
                        setUser(loggedUser);
                        showToast(`Welcome, ${loggedUser.name}!`);
                    }}
                />
            </>
        );
    }

    return (
        <div className="min-h-screen flex bg-neutral-100 text-neutral-900 dark:bg-neutral-950 dark:text-neutral-100">
            <Head title={`Admin Console — ${currentTab.toUpperCase()}`} />

            {/* 1. Left Navigation Side Panel */}
            <AdminSidebar
                currentTab={currentTab}
                onTabChange={handleTabChange}
                user={user}
                onLogout={handleLogout}
                isCollapsed={isSidebarCollapsed}
                onToggleCollapse={() => setIsSidebarCollapsed(prev => !prev)}
                isOpenMobile={isMobileSidebarOpen}
                onCloseMobile={() => setIsMobileSidebarOpen(false)}
                metrics={metrics}
                onOpenRegisterSpecimen={() => {
                    setSpecimenToEdit(null);
                    setIsSpecimenModalOpen(true);
                }}
                onOpenIssueLoan={() => {
                    setLoanPreSelectedSpecimen(null);
                    setIsLoanModalOpen(true);
                }}
                isDark={isDark}
                onToggleDark={toggleDarkMode}
            />

            {/* 2. Main Workstation Area */}
            <div className="flex-1 flex flex-col min-w-0 min-h-screen">
                <AdminHeader
                    currentTab={currentTab}
                    onTabChange={handleTabChange}
                    user={user}
                    onLogout={handleLogout}
                    isDark={isDark}
                    onToggleDark={toggleDarkMode}
                    isSidebarCollapsed={isSidebarCollapsed}
                    onToggleSidebar={() => setIsSidebarCollapsed(prev => !prev)}
                    onOpenMobileSidebar={() => setIsMobileSidebarOpen(true)}
                    isSidePanelOpen={isSidePanelOpen}
                    onToggleSidePanel={() => setIsSidePanelOpen(prev => !prev)}
                    onRefresh={loadData}
                    isLoading={isLoading}
                />

                {/* Toast Banner */}
                {toastMessage && (
                    <div className="fixed bottom-4 right-4 z-50 flex items-center gap-2 rounded-xs border border-neutral-950 bg-neutral-950 px-4 py-2.5 text-xs text-white shadow-lg dark:border-neutral-100 dark:bg-neutral-100 dark:text-neutral-950">
                        <CheckCircle2 className="size-4 shrink-0" />
                        <span>{toastMessage.text}</span>
                    </div>
                )}

                {/* Content Area with Docked Workstation Side Panel */}
                <div className="flex-1 flex min-w-0 overflow-hidden relative">
                    <main className="flex-1 min-w-0 p-4 sm:p-6 space-y-6 overflow-y-auto">

                {/* ========================================================================= */}
                {/* 1. OVERVIEW TAB */}
                {/* ========================================================================= */}
                {currentTab === 'overview' && (
                    <div className="space-y-6">
                        {/* KPI Metrics Row */}
                        <div className="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-7">
                            <div className="rounded-sm border border-neutral-200 bg-white p-3 dark:border-neutral-800 dark:bg-neutral-900">
                                <span className="block text-[10px] font-mono uppercase tracking-wider text-neutral-500">Total Specimens</span>
                                <span className="text-xl font-bold tracking-tight text-neutral-950 dark:text-neutral-50">
                                    {metrics?.specimens?.total ?? 0}
                                </span>
                            </div>
                            <div className="rounded-sm border border-neutral-200 bg-white p-3 dark:border-neutral-800 dark:bg-neutral-900">
                                <span className="block text-[10px] font-mono uppercase tracking-wider text-neutral-500">Active Loans</span>
                                <span className="text-xl font-bold tracking-tight text-neutral-950 dark:text-neutral-50">
                                    {metrics?.loans?.active ?? 0}
                                </span>
                            </div>
                            <div className="rounded-sm border-2 border-neutral-950 bg-black text-white p-3 dark:border-white dark:bg-white dark:text-black">
                                <span className="block text-[10px] font-mono uppercase tracking-wider text-neutral-300 dark:text-neutral-700">Overdue Loans</span>
                                <span className="text-xl font-bold tracking-tight">
                                    {metrics?.loans?.overdue ?? 0}
                                </span>
                            </div>
                            <div className="rounded-sm border border-neutral-200 bg-white p-3 dark:border-neutral-800 dark:bg-neutral-900">
                                <span className="block text-[10px] font-mono uppercase tracking-wider text-neutral-500">Taxa Tree</span>
                                <span className="text-xl font-bold tracking-tight text-neutral-950 dark:text-neutral-50">
                                    {metrics?.totals?.taxa ?? 0}
                                </span>
                            </div>
                            <div className="rounded-sm border border-neutral-200 bg-white p-3 dark:border-neutral-800 dark:bg-neutral-900">
                                <span className="block text-[10px] font-mono uppercase tracking-wider text-neutral-500">Collections</span>
                                <span className="text-xl font-bold tracking-tight text-neutral-950 dark:text-neutral-50">
                                    {metrics?.totals?.collections ?? 0}
                                </span>
                            </div>
                            <div className="rounded-sm border border-neutral-200 bg-white p-3 dark:border-neutral-800 dark:bg-neutral-900">
                                <span className="block text-[10px] font-mono uppercase tracking-wider text-neutral-500">Researchers</span>
                                <span className="text-xl font-bold tracking-tight text-neutral-950 dark:text-neutral-50">
                                    {metrics?.totals?.researchers ?? 0}
                                </span>
                            </div>
                            <div className="rounded-sm border border-neutral-200 bg-white p-3 dark:border-neutral-800 dark:bg-neutral-900">
                                <span className="block text-[10px] font-mono uppercase tracking-wider text-neutral-500">Projects</span>
                                <span className="text-xl font-bold tracking-tight text-neutral-950 dark:text-neutral-50">
                                    {metrics?.totals?.projects ?? 0}
                                </span>
                            </div>
                        </div>

                        {/* Status Distribution Bars (Achromatic Grayscale) */}
                        <div className="grid grid-cols-1 gap-4 lg:grid-cols-2">
                            {/* Specimen Status Breakdown */}
                            <div className="rounded-sm border border-neutral-200 bg-white p-4 dark:border-neutral-800 dark:bg-neutral-900">
                                <div className="flex items-center justify-between mb-3">
                                    <h3 className="text-xs font-mono uppercase tracking-wider text-neutral-900 dark:text-neutral-100">
                                        Specimens Distribution by Status
                                    </h3>
                                    <button onClick={() => handleTabChange('specimens')} className="text-[11px] text-neutral-500 hover:text-black dark:hover:text-white flex items-center gap-1">
                                        <span>View catalog</span>
                                        <ArrowUpRight className="size-3" />
                                    </button>
                                </div>
                                <div className="space-y-2">
                                    {metrics?.specimens?.by_status && Object.entries(metrics.specimens.by_status).map(([status, count]: [string, any]) => {
                                        const pct = Math.round((count / (metrics?.specimens?.total || 1)) * 100);
                                        return (
                                            <div key={status} className="space-y-1 text-xs">
                                                <div className="flex justify-between text-neutral-600 dark:text-neutral-400">
                                                    <span className="uppercase font-mono">{status}</span>
                                                    <span>{count} ({pct}%)</span>
                                                </div>
                                                <div className="h-2 w-full rounded-xs bg-neutral-100 overflow-hidden dark:bg-neutral-800">
                                                    <div 
                                                        className={`h-full ${
                                                            status === 'active' 
                                                                ? 'bg-neutral-950 dark:bg-neutral-100' 
                                                                : status === 'loaned'
                                                                ? 'bg-neutral-700 dark:bg-neutral-400'
                                                                : 'bg-neutral-400 dark:bg-neutral-600'
                                                        }`} 
                                                        style={{ width: `${pct}%` }} 
                                                    />
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                            </div>

                            {/* Loan Status Breakdown */}
                            <div className="rounded-sm border border-neutral-200 bg-white p-4 dark:border-neutral-800 dark:bg-neutral-900">
                                <div className="flex items-center justify-between mb-3">
                                    <h3 className="text-xs font-mono uppercase tracking-wider text-neutral-900 dark:text-neutral-100">
                                        Loans Pipeline Breakdown
                                    </h3>
                                    <button onClick={() => handleTabChange('loans')} className="text-[11px] text-neutral-500 hover:text-black dark:hover:text-white flex items-center gap-1">
                                        <span>Manage loans</span>
                                        <ArrowUpRight className="size-3" />
                                    </button>
                                </div>
                                <div className="space-y-2">
                                    {metrics?.loans?.by_status && Object.entries(metrics.loans.by_status).map(([status, count]: [string, any]) => {
                                        const pct = Math.round((count / (metrics?.loans?.total || 1)) * 100);
                                        return (
                                            <div key={status} className="space-y-1 text-xs">
                                                <div className="flex justify-between text-neutral-600 dark:text-neutral-400">
                                                    <span className="uppercase font-mono">{status}</span>
                                                    <span>{count} ({pct}%)</span>
                                                </div>
                                                <div className="h-2 w-full rounded-xs bg-neutral-100 overflow-hidden dark:bg-neutral-800">
                                                    <div 
                                                        className={`h-full ${
                                                            status === 'approved' 
                                                                ? 'bg-neutral-950 dark:bg-neutral-100' 
                                                                : status === 'returned'
                                                                ? 'bg-neutral-600 dark:bg-neutral-400'
                                                                : 'bg-neutral-400 dark:bg-neutral-600'
                                                        }`} 
                                                        style={{ width: `${pct}%` }} 
                                                    />
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                            </div>
                        </div>

                        {/* Urgent Overdue Loans Section */}
                        {metrics?.loans?.overdue > 0 && (
                            <div className="rounded-sm border-2 border-neutral-950 bg-white p-4 dark:border-white dark:bg-neutral-900">
                                <div className="flex items-center justify-between mb-3">
                                    <div className="flex items-center gap-2">
                                        <AlertTriangle className="size-4 text-neutral-950 dark:text-white" />
                                        <h3 className="text-xs font-bold uppercase tracking-wider text-neutral-950 dark:text-neutral-50">
                                            Action Required: Overdue Specimen Loans ({metrics.loans.overdue})
                                        </h3>
                                    </div>
                                    <span className="text-[10px] font-mono text-neutral-500">Past Expected Return Date</span>
                                </div>
                                <div className="overflow-x-auto">
                                    <table className="w-full text-left text-xs">
                                        <thead>
                                            <tr className="border-b border-neutral-200 dark:border-neutral-800 text-neutral-500 font-mono text-[10px] uppercase">
                                                <th className="pb-2">Specimen</th>
                                                <th className="pb-2">Borrower</th>
                                                <th className="pb-2">Issue Date</th>
                                                <th className="pb-2">Expected Return</th>
                                                <th className="pb-2 text-right">Workflow Action</th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-neutral-100 dark:divide-neutral-800">
                                            {loans.filter(l => l.status === 'approved' && new Date(l.expected_return_date) < new Date()).slice(0, 5).map((ln) => (
                                                <tr key={ln.id} className="hover:bg-neutral-50 dark:hover:bg-neutral-800/50">
                                                    <td className="py-2.5 font-mono font-medium">{ln.specimen?.specimen_code || 'VOUCHER'}</td>
                                                    <td className="py-2.5">{ln.borrower_name}</td>
                                                    <td className="py-2.5 text-neutral-500">{ln.loan_date}</td>
                                                    <td className="py-2.5 font-bold">{ln.expected_return_date}</td>
                                                    <td className="py-2.5 text-right">
                                                        <button
                                                            onClick={() => handleReturnLoan(ln.id)}
                                                            className="rounded-sm border border-neutral-950 bg-black px-2.5 py-1 text-[10px] font-medium text-white hover:opacity-90 dark:border-white dark:bg-white dark:text-black"
                                                        >
                                                            Record Return
                                                        </button>
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        )}

                        {/* Recent Specimens Table */}
                        <div className="rounded-sm border border-neutral-200 bg-white p-4 dark:border-neutral-800 dark:bg-neutral-900">
                            <div className="flex items-center justify-between mb-3">
                                <h3 className="text-xs font-mono uppercase tracking-wider text-neutral-900 dark:text-neutral-100">
                                    Recent Specimens in Catalog
                                </h3>
                                <button onClick={() => setIsSpecimenModalOpen(true)} className="flex items-center gap-1 text-xs font-medium text-neutral-900 hover:underline dark:text-neutral-100">
                                    <Plus className="size-3.5" />
                                    <span>Register Voucher</span>
                                </button>
                            </div>
                            <div className="overflow-x-auto">
                                <table className="w-full text-left text-xs">
                                    <thead>
                                        <tr className="border-b border-neutral-200 dark:border-neutral-800 text-neutral-500 font-mono text-[10px] uppercase">
                                            <th className="pb-2">Code</th>
                                            <th className="pb-2">Scientific Name</th>
                                            <th className="pb-2">Common Name</th>
                                            <th className="pb-2">Preservation</th>
                                            <th className="pb-2">Storage Unit</th>
                                            <th className="pb-2">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-neutral-100 dark:divide-neutral-800">
                                        {specimens.slice(0, 8).map((sp) => (
                                            <tr 
                                                key={sp.id} 
                                                className={`cursor-pointer transition-colors ${
                                                    selectedSidePanelSpecimen?.id === sp.id 
                                                        ? 'bg-neutral-100 dark:bg-neutral-800' 
                                                        : 'hover:bg-neutral-50 dark:hover:bg-neutral-800/50'
                                                }`} 
                                                onClick={() => { setSelectedSidePanelSpecimen(sp); setIsSidePanelOpen(true); }}
                                            >
                                                <td className="py-2.5 font-mono font-medium">{sp.specimen_code}</td>
                                                <td className="py-2.5 italic font-medium">{sp.scientific_name}</td>
                                                <td className="py-2.5 text-neutral-600 dark:text-neutral-400">{sp.common_name || '—'}</td>
                                                <td className="py-2.5 font-mono uppercase text-[10px]">{sp.preservation_method}</td>
                                                <td className="py-2.5 text-neutral-500">{sp.repository_location}</td>
                                                <td className="py-2.5">
                                                    <span className={`inline-block rounded-xs px-1.5 py-0.5 text-[10px] font-bold uppercase ${
                                                        sp.status === 'active' 
                                                            ? 'bg-black text-white dark:bg-white dark:text-black' 
                                                            : 'border border-neutral-400 text-neutral-600 dark:border-neutral-600 dark:text-neutral-300'
                                                    }`}>
                                                        {sp.status}
                                                    </span>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                )}

                {/* ========================================================================= */}
                {/* 2. SPECIMENS CATALOG TAB */}
                {/* ========================================================================= */}
                {currentTab === 'specimens' && (
                    <div className="space-y-4">
                        {/* Action & Filter Header */}
                        <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div className="flex flex-1 flex-wrap items-center gap-2">
                                <div className="relative min-w-[240px] flex-1">
                                    <Search className="pointer-events-none absolute left-3 top-2.5 size-3.5 text-neutral-400" />
                                    <input
                                        type="text"
                                        placeholder="Search by code, scientific name, catalog #..."
                                        value={specimenSearch}
                                        onChange={(e) => setSpecimenSearch(e.target.value)}
                                        onKeyDown={(e) => e.key === 'Enter' && loadData()}
                                        className="w-full rounded-sm border border-neutral-300 bg-white py-1.5 pl-8 pr-3 text-xs dark:border-neutral-700 dark:bg-neutral-900"
                                    />
                                </div>
                                <select
                                    value={specimenStatusFilter}
                                    onChange={(e) => setSpecimenStatusFilter(e.target.value)}
                                    className="rounded-sm border border-neutral-300 bg-white px-2.5 py-1.5 text-xs dark:border-neutral-700 dark:bg-neutral-900"
                                >
                                    <option value="">All Statuses</option>
                                    <option value="active">Active</option>
                                    <option value="loaned">Loaned</option>
                                    <option value="under_review">Under Review</option>
                                    <option value="archived">Archived</option>
                                </select>
                                <select
                                    value={specimenPresFilter}
                                    onChange={(e) => setSpecimenPresFilter(e.target.value)}
                                    className="rounded-sm border border-neutral-300 bg-white px-2.5 py-1.5 text-xs dark:border-neutral-700 dark:bg-neutral-900"
                                >
                                    <option value="">All Preservation</option>
                                    <option value="wet">Wet</option>
                                    <option value="dry">Dry</option>
                                    <option value="frozen">Frozen</option>
                                    <option value="skeletal">Skeletal</option>
                                    <option value="pinned">Pinned</option>
                                    <option value="slide">Slide</option>
                                    <option value="tissue">Tissue</option>
                                </select>
                                <button
                                    onClick={loadData}
                                    className="rounded-sm border border-neutral-300 bg-white p-2 text-neutral-700 hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300"
                                    title="Refresh"
                                >
                                    <RefreshCw className={`size-3.5 ${isLoading ? 'animate-spin' : ''}`} />
                                </button>
                            </div>

                            <button
                                onClick={() => { setSpecimenToEdit(null); setIsSpecimenModalOpen(true); }}
                                className="flex items-center gap-1.5 rounded-sm bg-black px-3.5 py-2 text-xs font-medium text-white hover:opacity-90 dark:bg-white dark:text-black shrink-0"
                            >
                                <Plus className="size-4" />
                                <span>Register Specimen</span>
                            </button>
                        </div>

                        {/* Specimens Data Table */}
                        <div className="rounded-sm border border-neutral-200 bg-white shadow-xs dark:border-neutral-800 dark:bg-neutral-900 overflow-hidden">
                            <div className="overflow-x-auto">
                                <table className="w-full text-left text-xs">
                                    <thead>
                                        <tr className="border-b border-neutral-200 bg-neutral-50/50 dark:border-neutral-800 dark:bg-neutral-800/30 text-neutral-500 font-mono text-[10px] uppercase">
                                            <th className="p-3">Specimen Code</th>
                                            <th className="p-3">Taxonomic Scientific Name</th>
                                            <th className="p-3">Common Name</th>
                                            <th className="p-3">Preservation</th>
                                            <th className="p-3">Collection Archive</th>
                                            <th className="p-3">Status</th>
                                            <th className="p-3 text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-neutral-100 dark:divide-neutral-800">
                                        {specimens.map((sp) => (
                                            <tr 
                                                key={sp.id} 
                                                onClick={() => { setSelectedSidePanelSpecimen(sp); setIsSidePanelOpen(true); }}
                                                className={`cursor-pointer transition-colors ${
                                                    selectedSidePanelSpecimen?.id === sp.id 
                                                        ? 'bg-neutral-100 dark:bg-neutral-800/80' 
                                                        : 'hover:bg-neutral-50/80 dark:hover:bg-neutral-800/40'
                                                }`}
                                            >
                                                <td className="p-3 font-mono font-medium text-neutral-900 dark:text-neutral-100">
                                                    {sp.specimen_code}
                                                </td>
                                                <td className="p-3 italic font-medium text-neutral-950 dark:text-neutral-50">
                                                    {sp.scientific_name}
                                                </td>
                                                <td className="p-3 text-neutral-600 dark:text-neutral-400">
                                                    {sp.common_name || '—'}
                                                </td>
                                                <td className="p-3 font-mono uppercase text-[10px]">
                                                    {sp.preservation_method}
                                                </td>
                                                <td className="p-3 text-neutral-500">
                                                    {sp.collection?.name || 'General Archive'}
                                                </td>
                                                <td className="p-3">
                                                    <span className={`inline-block rounded-xs px-2 py-0.5 text-[10px] font-bold uppercase ${
                                                        sp.status === 'active' 
                                                            ? 'bg-black text-white dark:bg-white dark:text-black' 
                                                            : sp.status === 'loaned'
                                                            ? 'border border-neutral-600 text-neutral-900 dark:border-neutral-400 dark:text-neutral-100'
                                                            : 'border border-neutral-300 text-neutral-500 dark:border-neutral-700'
                                                    }`}>
                                                        {sp.status}
                                                    </span>
                                                </td>
                                                <td className="p-3 text-right">
                                                    <div className="flex items-center justify-end gap-1">
                                                        <button
                                                            onClick={() => {
                                                                setSelectedSidePanelSpecimen(sp);
                                                                setIsSidePanelOpen(true);
                                                            }}
                                                            title="Inspect in Side Panel"
                                                            className="rounded p-1 text-neutral-500 hover:bg-neutral-100 hover:text-black dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-white"
                                                        >
                                                            <PanelRightOpen className="size-3.5" />
                                                        </button>
                                                        <button
                                                            onClick={() => { setDetailSpecimen(sp); setIsDetailModalOpen(true); }}
                                                            title="Deep Inspector Modal"
                                                            className="rounded p-1 text-neutral-500 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:bg-neutral-800"
                                                        >
                                                            <Eye className="size-3.5" />
                                                        </button>
                                                        <button
                                                            onClick={() => { setSpecimenToEdit(sp); setIsSpecimenModalOpen(true); }}
                                                            title="Edit Details"
                                                            className="rounded p-1 text-neutral-500 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:bg-neutral-800"
                                                        >
                                                            <Edit3 className="size-3.5" />
                                                        </button>
                                                        <button
                                                            onClick={() => handleDeleteSpecimen(sp)}
                                                            title="Deaccession"
                                                            className="rounded p-1 text-neutral-500 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:bg-neutral-800"
                                                        >
                                                            <Trash2 className="size-3.5" />
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>

                            {/* Pagination */}
                            <div className="flex items-center justify-between border-t border-neutral-200 px-4 py-3 dark:border-neutral-800 text-xs">
                                <span className="text-neutral-500">
                                    Showing {specimens.length} of {specimenTotal} vouchers
                                </span>
                                <div className="flex gap-2">
                                    <button
                                        disabled={specimenPage <= 1}
                                        onClick={() => setSpecimenPage(p => Math.max(1, p - 1))}
                                        className="rounded-sm border border-neutral-300 px-3 py-1 text-xs disabled:opacity-30 dark:border-neutral-700"
                                    >
                                        Previous
                                    </button>
                                    <button
                                        onClick={() => setSpecimenPage(p => p + 1)}
                                        className="rounded-sm border border-neutral-300 px-3 py-1 text-xs dark:border-neutral-700"
                                    >
                                        Next
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                )}

                {/* ========================================================================= */}
                {/* 3. LOANS WORKFLOW TAB */}
                {/* ========================================================================= */}
                {currentTab === 'loans' && (
                    <div className="space-y-4">
                        {/* Header Controls */}
                        <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div className="flex flex-wrap items-center gap-2">
                                <div className="relative min-w-[240px]">
                                    <Search className="pointer-events-none absolute left-3 top-2.5 size-3.5 text-neutral-400" />
                                    <input
                                        type="text"
                                        placeholder="Search by borrower or specimen..."
                                        value={loanSearch}
                                        onChange={(e) => setLoanSearch(e.target.value)}
                                        onKeyDown={(e) => e.key === 'Enter' && loadData()}
                                        className="w-full rounded-sm border border-neutral-300 bg-white py-1.5 pl-8 pr-3 text-xs dark:border-neutral-700 dark:bg-neutral-900"
                                    />
                                </div>
                                <select
                                    value={loanStatusFilter}
                                    onChange={(e) => setLoanStatusFilter(e.target.value)}
                                    className="rounded-sm border border-neutral-300 bg-white px-2.5 py-1.5 text-xs dark:border-neutral-700 dark:bg-neutral-900"
                                >
                                    <option value="">All Loan Statuses</option>
                                    <option value="approved">Approved / Active</option>
                                    <option value="pending">Pending Review</option>
                                    <option value="returned">Returned</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                                <button
                                    onClick={loadData}
                                    className="rounded-sm border border-neutral-300 bg-white p-2 text-neutral-700 hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300"
                                >
                                    <RefreshCw className={`size-3.5 ${isLoading ? 'animate-spin' : ''}`} />
                                </button>
                            </div>

                            <button
                                onClick={() => { setLoanPreSelectedSpecimen(null); setIsLoanModalOpen(true); }}
                                className="flex items-center gap-1.5 rounded-sm bg-black px-3.5 py-2 text-xs font-medium text-white hover:opacity-90 dark:bg-white dark:text-black shrink-0"
                            >
                                <Plus className="size-4" />
                                <span>Create Loan Transaction</span>
                            </button>
                        </div>

                        {/* Loans Table */}
                        <div className="rounded-sm border border-neutral-200 bg-white shadow-xs dark:border-neutral-800 dark:bg-neutral-900 overflow-hidden">
                            <div className="overflow-x-auto">
                                <table className="w-full text-left text-xs">
                                    <thead>
                                        <tr className="border-b border-neutral-200 bg-neutral-50/50 dark:border-neutral-800 dark:bg-neutral-800/30 text-neutral-500 font-mono text-[10px] uppercase">
                                            <th className="p-3">Specimen Voucher</th>
                                            <th className="p-3">Borrower / Researcher</th>
                                            <th className="p-3">Research Purpose</th>
                                            <th className="p-3">Loan Period</th>
                                            <th className="p-3">Status</th>
                                            <th className="p-3 text-right">Workflow Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-neutral-100 dark:divide-neutral-800">
                                        {loans.map((ln) => {
                                            const isOverdue = ln.status === 'approved' && new Date(ln.expected_return_date) < new Date();
                                            return (
                                                <tr key={ln.id} className="hover:bg-neutral-50/80 dark:hover:bg-neutral-800/40">
                                                    <td className="p-3">
                                                        <span className="font-mono font-semibold text-neutral-900 dark:text-neutral-100">
                                                            {ln.specimen?.specimen_code || 'SPECIMEN'}
                                                        </span>
                                                        <p className="text-[11px] italic text-neutral-500">
                                                            {ln.specimen?.scientific_name}
                                                        </p>
                                                    </td>
                                                    <td className="p-3">
                                                        <span className="font-medium">{ln.borrower_name}</span>
                                                        <p className="text-[10px] text-neutral-500">{ln.researcher?.institution || 'External Institution'}</p>
                                                    </td>
                                                    <td className="p-3 max-w-xs truncate text-neutral-700 dark:text-neutral-300" title={ln.purpose}>
                                                        {ln.purpose}
                                                    </td>
                                                    <td className="p-3 text-[11px]">
                                                        <div>Out: <span className="text-neutral-500">{ln.loan_date}</span></div>
                                                        <div>Due: <span className={isOverdue ? 'font-bold underline' : 'text-neutral-500'}>{ln.expected_return_date}</span></div>
                                                    </td>
                                                    <td className="p-3">
                                                        {isOverdue ? (
                                                            <span className="rounded-xs border-2 border-black bg-black px-1.5 py-0.5 text-[9px] font-bold uppercase text-white dark:border-white dark:bg-white dark:text-black">
                                                                Overdue
                                                            </span>
                                                        ) : (
                                                            <span className={`inline-block rounded-xs px-2 py-0.5 text-[10px] font-bold uppercase ${
                                                                ln.status === 'approved' 
                                                                    ? 'bg-black text-white dark:bg-white dark:text-black' 
                                                                    : ln.status === 'returned'
                                                                    ? 'border border-neutral-400 text-neutral-600 dark:border-neutral-600 dark:text-neutral-300'
                                                                    : 'border border-neutral-300 text-neutral-400 dark:border-neutral-700'
                                                            }`}>
                                                                {ln.status}
                                                            </span>
                                                        )}
                                                    </td>
                                                    <td className="p-3 text-right">
                                                        <div className="flex items-center justify-end gap-1.5">
                                                            {ln.status === 'pending' && (
                                                                <>
                                                                    <button
                                                                        onClick={() => handleApproveLoan(ln.id)}
                                                                        className="flex items-center gap-1 rounded-sm bg-black px-2.5 py-1 text-[11px] font-medium text-white hover:opacity-90 dark:bg-white dark:text-black"
                                                                    >
                                                                        <CheckCircle2 className="size-3" />
                                                                        <span>Approve</span>
                                                                    </button>
                                                                    <button
                                                                        onClick={() => handleCancelLoan(ln.id)}
                                                                        className="rounded-sm border border-neutral-300 px-2 py-1 text-[11px] hover:bg-neutral-100 dark:border-neutral-700 dark:hover:bg-neutral-800"
                                                                    >
                                                                        <XCircle className="size-3" />
                                                                    </button>
                                                                </>
                                                            )}
                                                            {ln.status === 'approved' && (
                                                                <button
                                                                    onClick={() => handleReturnLoan(ln.id)}
                                                                    className="flex items-center gap-1 rounded-sm border border-neutral-900 bg-neutral-900 px-2.5 py-1 text-[11px] font-medium text-white hover:opacity-90 dark:border-neutral-100 dark:bg-neutral-100 dark:text-neutral-950"
                                                                >
                                                                    <RotateCcw className="size-3" />
                                                                    <span>Return Voucher</span>
                                                                </button>
                                                            )}
                                                        </div>
                                                    </td>
                                                </tr>
                                            );
                                        })}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                )}

                {/* ========================================================================= */}
                {/* 4. TAXONOMY TAB */}
                {/* ========================================================================= */}
                {currentTab === 'taxa' && (
                    <div className="space-y-4">
                        <div className="flex items-center justify-between">
                            <div>
                                <h2 className="text-sm font-semibold tracking-tight text-neutral-950 dark:text-neutral-50">
                                    Taxonomic Tree Classification
                                </h2>
                                <p className="text-xs text-neutral-500">
                                    Explore binomial nomenclature across Kingdom, Phylum, Class, Order, Family, Genus, and Species.
                                </p>
                            </div>
                            <button
                                onClick={() => setIsTaxonModalOpen(true)}
                                className="flex items-center gap-1.5 rounded-sm bg-black px-3.5 py-2 text-xs font-medium text-white hover:opacity-90 dark:bg-white dark:text-black"
                            >
                                <Plus className="size-4" />
                                <span>Add Classification</span>
                            </button>
                        </div>

                        <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            {taxa.map((t) => (
                                <div key={t.id} className="rounded-sm border border-neutral-200 bg-white p-4 dark:border-neutral-800 dark:bg-neutral-900 space-y-2">
                                    <div className="flex items-start justify-between">
                                        <div>
                                            <h4 className="font-bold italic text-sm text-neutral-950 dark:text-neutral-50">
                                                {t.scientific_name}
                                            </h4>
                                            <p className="text-xs text-neutral-500">{t.common_name || 'No common name'}</p>
                                        </div>
                                        <span className="rounded-xs border border-neutral-300 px-1.5 py-0.5 font-mono text-[9px] uppercase dark:border-neutral-700">
                                            {t.kingdom}
                                        </span>
                                    </div>
                                    <div className="text-[11px] text-neutral-600 dark:text-neutral-400 space-y-0.5 pt-1 border-t border-neutral-100 dark:border-neutral-800">
                                        <div>Class: <span className="font-medium text-neutral-900 dark:text-neutral-100">{t.class || '—'}</span></div>
                                        <div>Order: <span className="font-medium text-neutral-900 dark:text-neutral-100">{t.order || '—'}</span></div>
                                        <div>Family: <span className="font-medium text-neutral-900 dark:text-neutral-100">{t.family || '—'}</span></div>
                                        {t.authority && <div>Authority: <span className="font-mono text-[10px]">{t.authority}</span></div>}
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                )}

                {/* ========================================================================= */}
                {/* 5. COLLECTIONS TAB */}
                {/* ========================================================================= */}
                {currentTab === 'collections' && (
                    <div className="space-y-4">
                        <div className="flex items-center justify-between">
                            <div>
                                <h2 className="text-sm font-semibold tracking-tight text-neutral-950 dark:text-neutral-50">
                                    Natural History Collections & Repositories
                                </h2>
                                <p className="text-xs text-neutral-500">
                                    Physical archives, historical surveys, and herbarium collections.
                                </p>
                            </div>
                            <button
                                onClick={() => setIsCollectionModalOpen(true)}
                                className="flex items-center gap-1.5 rounded-sm bg-black px-3.5 py-2 text-xs font-medium text-white hover:opacity-90 dark:bg-white dark:text-black"
                            >
                                <Plus className="size-4" />
                                <span>New Archive</span>
                            </button>
                        </div>

                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            {collections.map((c) => (
                                <div key={c.id} className="rounded-sm border border-neutral-200 bg-white p-4 dark:border-neutral-800 dark:bg-neutral-900 flex flex-col justify-between">
                                    <div>
                                        <div className="flex items-center justify-between mb-2">
                                            <span className="rounded-xs border border-neutral-300 px-1.5 py-0.5 text-[10px] font-mono uppercase text-neutral-600 dark:border-neutral-700 dark:text-neutral-400">
                                                {c.collection_type || 'General'}
                                            </span>
                                            <span className="text-[10px] font-bold uppercase">{c.status}</span>
                                        </div>
                                        <h3 className="font-semibold text-sm text-neutral-950 dark:text-neutral-50 mb-1">
                                            {c.name}
                                        </h3>
                                        <p className="text-xs text-neutral-500 line-clamp-3 mb-3">
                                            {c.description || 'No description recorded.'}
                                        </p>
                                    </div>
                                    <div className="text-[11px] text-neutral-600 dark:text-neutral-400 pt-2 border-t border-neutral-100 dark:border-neutral-800 flex justify-between">
                                        <span>Curator: {c.collector || 'Staff'}</span>
                                        <span>Since {c.date_started?.split('-')[0] || '2024'}</span>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                )}

                {/* ========================================================================= */}
                {/* 6. LOCALITIES TAB */}
                {/* ========================================================================= */}
                {currentTab === 'locations' && (
                    <div className="space-y-4">
                        <div className="flex items-center justify-between">
                            <div>
                                <h2 className="text-sm font-semibold tracking-tight text-neutral-950 dark:text-neutral-50">
                                    Field Sampling Localities
                                </h2>
                                <p className="text-xs text-neutral-500">
                                    Georeferenced field stations, national parks, and atoll coordinates.
                                </p>
                            </div>
                            <button
                                onClick={() => setIsLocationModalOpen(true)}
                                className="flex items-center gap-1.5 rounded-sm bg-black px-3.5 py-2 text-xs font-medium text-white hover:opacity-90 dark:bg-white dark:text-black"
                            >
                                <Plus className="size-4" />
                                <span>Register Locality</span>
                            </button>
                        </div>

                        <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            {locations.map((loc) => (
                                <div key={loc.id} className="rounded-sm border border-neutral-200 bg-white p-4 dark:border-neutral-800 dark:bg-neutral-900 space-y-2">
                                    <div className="flex items-start justify-between">
                                        <h4 className="font-semibold text-sm text-neutral-950 dark:text-neutral-50">
                                            {loc.name}
                                        </h4>
                                        <span className="font-mono text-[10px] text-neutral-500">
                                            {loc.province}
                                        </span>
                                    </div>
                                    <div className="font-mono text-xs text-neutral-700 dark:text-neutral-300">
                                        {loc.latitude}°N, {loc.longitude}°E
                                    </div>
                                    <p className="text-xs text-neutral-500 line-clamp-2">
                                        {loc.habitat || loc.description || 'Habitat description not recorded.'}
                                    </p>
                                </div>
                            ))}
                        </div>
                    </div>
                )}

                {/* ========================================================================= */}
                {/* 7. RESEARCHERS TAB */}
                {/* ========================================================================= */}
                {currentTab === 'researchers' && (
                    <div className="space-y-4">
                        <div className="flex items-center justify-between">
                            <div>
                                <h2 className="text-sm font-semibold tracking-tight text-neutral-950 dark:text-neutral-50">
                                    Scientific Researchers & Investigators
                                </h2>
                                <p className="text-xs text-neutral-500">
                                    Institutional directory of researchers authorized for specimen examination and loans.
                                </p>
                            </div>
                            <button
                                onClick={() => setIsResearcherModalOpen(true)}
                                className="flex items-center gap-1.5 rounded-sm bg-black px-3.5 py-2 text-xs font-medium text-white hover:opacity-90 dark:bg-white dark:text-black"
                            >
                                <Plus className="size-4" />
                                <span>Register Investigator</span>
                            </button>
                        </div>

                        <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            {researchers.map((r) => (
                                <div key={r.id} className="rounded-sm border border-neutral-200 bg-white p-4 dark:border-neutral-800 dark:bg-neutral-900 space-y-2">
                                    <div className="flex items-start justify-between">
                                        <h4 className="font-semibold text-sm text-neutral-950 dark:text-neutral-50">
                                            {r.name}
                                        </h4>
                                        <span className="text-[10px] font-mono uppercase text-neutral-400">
                                            {r.status || 'Active'}
                                        </span>
                                    </div>
                                    <p className="text-xs font-medium text-neutral-700 dark:text-neutral-300">
                                        {r.institution}
                                    </p>
                                    <p className="text-[11px] text-neutral-500">
                                        {r.specialization || r.department}
                                    </p>
                                    <div className="text-xs font-mono text-neutral-600 dark:text-neutral-400 pt-2 border-t border-neutral-100 dark:border-neutral-800">
                                        {r.email}
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                )}

                {/* ========================================================================= */}
                {/* 8. AUDIT LOGS TAB */}
                {/* ========================================================================= */}
                {currentTab === 'audit-logs' && (
                    <div className="space-y-4">
                        <div className="flex items-center justify-between">
                            <div>
                                <h2 className="text-sm font-semibold tracking-tight text-neutral-950 dark:text-neutral-50">
                                    Administrative Security Audit Trail
                                </h2>
                                <p className="text-xs text-neutral-500">
                                    Verifiable record of state-changing administrative operations, authentication events, and transactions.
                                </p>
                            </div>
                            <button
                                onClick={loadData}
                                className="flex items-center gap-1.5 rounded-sm border border-neutral-300 px-3 py-1.5 text-xs text-neutral-700 hover:bg-neutral-50 dark:border-neutral-700 dark:text-neutral-300"
                            >
                                <RefreshCw className={`size-3.5 ${isLoading ? 'animate-spin' : ''}`} />
                                <span>Refresh Logs</span>
                            </button>
                        </div>

                        <div className="rounded-sm border border-neutral-200 bg-white shadow-xs dark:border-neutral-800 dark:bg-neutral-900 overflow-hidden">
                            <div className="overflow-x-auto">
                                <table className="w-full text-left text-xs">
                                    <thead>
                                        <tr className="border-b border-neutral-200 bg-neutral-50/50 dark:border-neutral-800 dark:bg-neutral-800/30 text-neutral-500 font-mono text-[10px] uppercase">
                                            <th className="p-3">Action</th>
                                            <th className="p-3">Admin</th>
                                            <th className="p-3">Description</th>
                                            <th className="p-3">IP Address</th>
                                            <th className="p-3">Timestamp</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-neutral-100 dark:divide-neutral-800">
                                        {auditLogs.map((log) => (
                                            <tr key={log.id} className="hover:bg-neutral-50/80 dark:hover:bg-neutral-800/40">
                                                <td className="p-3 font-mono font-bold text-[10px]">
                                                    <span className="rounded-xs border border-neutral-300 px-1.5 py-0.5 dark:border-neutral-700">
                                                        {log.action}
                                                    </span>
                                                </td>
                                                <td className="p-3 font-mono text-neutral-700 dark:text-neutral-300">
                                                    {log.admin_email}
                                                </td>
                                                <td className="p-3 max-w-md text-neutral-900 dark:text-neutral-100">
                                                    {log.description}
                                                </td>
                                                <td className="p-3 font-mono text-[11px] text-neutral-500">
                                                    {log.ip_address || '127.0.0.1'}
                                                </td>
                                                <td className="p-3 font-mono text-[11px] text-neutral-500 whitespace-nowrap">
                                                    {log.created_at?.replace('T', ' ').substring(0, 19) || 'Just now'}
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                )}
                    </main>

                    {/* 3. Right Curatorial Inspector Side Panel */}
                    <AdminSidePanel
                        isOpen={isSidePanelOpen}
                        onClose={() => setIsSidePanelOpen(false)}
                        selectedSpecimen={selectedSidePanelSpecimen}
                        onClearSelectedSpecimen={() => setSelectedSidePanelSpecimen(null)}
                        onInspectModal={(s) => {
                            setDetailSpecimen(s);
                            setIsDetailModalOpen(true);
                        }}
                        onEditSpecimen={(s) => {
                            setSpecimenToEdit(s);
                            setIsSpecimenModalOpen(true);
                        }}
                        onCreateLoanForSpecimen={(s) => {
                            setLoanPreSelectedSpecimen(s);
                            setIsLoanModalOpen(true);
                        }}
                        metrics={metrics}
                        loans={loans}
                        auditLogs={auditLogs}
                        onRecordReturn={handleReturnLoan}
                        onOpenRegisterSpecimen={() => {
                            setSpecimenToEdit(null);
                            setIsSpecimenModalOpen(true);
                        }}
                        onOpenIssueLoan={() => {
                            setLoanPreSelectedSpecimen(null);
                            setIsLoanModalOpen(true);
                        }}
                        onSelectSpecimen={(s) => setSelectedSidePanelSpecimen(s)}
                    />
                </div>
            </div>

            {/* Modals Container */}
            <SpecimenModal
                isOpen={isSpecimenModalOpen}
                onClose={() => { setIsSpecimenModalOpen(false); setSpecimenToEdit(null); }}
                onSaved={loadData}
                specimen={specimenToEdit}
                taxa={taxa}
                collections={collections}
                locations={locations}
                researchers={researchers}
            />

            <SpecimenDetailModal
                isOpen={isDetailModalOpen}
                onClose={() => { setIsDetailModalOpen(false); setDetailSpecimen(null); }}
                specimen={detailSpecimen}
                onEdit={(sp) => {
                    setDetailSpecimen(null);
                    setSpecimenToEdit(sp);
                    setIsSpecimenModalOpen(true);
                }}
                onCreateLoan={(sp) => {
                    setDetailSpecimen(null);
                    setLoanPreSelectedSpecimen(sp);
                    setIsLoanModalOpen(true);
                }}
            />

            <LoanModal
                isOpen={isLoanModalOpen}
                onClose={() => { setIsLoanModalOpen(false); setLoanPreSelectedSpecimen(null); }}
                onCreated={loadData}
                preSelectedSpecimen={loanPreSelectedSpecimen}
                activeSpecimens={specimens.filter(s => s.status === 'active')}
                researchers={researchers}
            />

            <TaxonModal
                isOpen={isTaxonModalOpen}
                onClose={() => setIsTaxonModalOpen(false)}
                onCreated={loadData}
            />

            <CollectionModal
                isOpen={isCollectionModalOpen}
                onClose={() => setIsCollectionModalOpen(false)}
                onCreated={loadData}
            />

            <LocationModal
                isOpen={isLocationModalOpen}
                onClose={() => setIsLocationModalOpen(false)}
                onCreated={loadData}
            />

            <ResearcherModal
                isOpen={isResearcherModalOpen}
                onClose={() => setIsResearcherModalOpen(false)}
                onCreated={loadData}
            />
        </div>
    );
}

// Bypass default starter layout for administrative console
(AdminDashboard as any).layout = null;
