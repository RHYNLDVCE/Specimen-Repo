import React from 'react';
import { 
    X, 
    PanelRightClose, 
    Layers, 
    Calendar, 
    MapPin, 
    Building2, 
    Eye, 
    Edit3, 
    GitPullRequest, 
    AlertTriangle, 
    CheckCircle2, 
    Archive,
    ExternalLink,
    Compass,
    Plus,
    RotateCcw,
    Activity,
    Database,
    ShieldCheck,
    Clock
} from 'lucide-react';

interface AdminSidePanelProps {
    isOpen: boolean;
    onClose: () => void;
    selectedSpecimen: any | null;
    onClearSelectedSpecimen?: () => void;
    onInspectModal: (specimen: any) => void;
    onEditSpecimen: (specimen: any) => void;
    onCreateLoanForSpecimen: (specimen: any) => void;
    metrics: any;
    loans?: any[];
    auditLogs?: any[];
    onRecordReturn?: (loanId: string) => void;
    onOpenRegisterSpecimen?: () => void;
    onOpenIssueLoan?: () => void;
    onSelectSpecimen?: (specimen: any) => void;
}

export const AdminSidePanel: React.FC<AdminSidePanelProps> = ({
    isOpen,
    onClose,
    selectedSpecimen,
    onClearSelectedSpecimen,
    onInspectModal,
    onEditSpecimen,
    onCreateLoanForSpecimen,
    metrics,
    loans = [],
    auditLogs = [],
    onRecordReturn,
    onOpenRegisterSpecimen,
    onOpenIssueLoan,
    onSelectSpecimen,
}) => {
    if (!isOpen) return null;

    // Extract overdue loans
    const overdueLoans = loans.filter(
        l => l.status === 'approved' && l.expected_return_date && new Date(l.expected_return_date) < new Date()
    );

    // Preservation breakdown
    const preservationCounts = [
        { label: 'Wet / Fluid', count: 24, percent: 21 },
        { label: 'Frozen Cryo', count: 22, percent: 19 },
        { label: 'Dry / Herbarium', count: 20, percent: 18 },
        { label: 'Microscope Slide', count: 18, percent: 16 },
        { label: 'Skeletal Mount', count: 16, percent: 14 },
        { label: 'Pinned Arthropod', count: 14, percent: 12 },
    ];

    return (
        <aside 
            className={`
                shrink-0 border-l border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-950 text-neutral-900 dark:text-neutral-100 flex flex-col
                transition-all duration-300 z-30
                /* Fixed slide-over on small/medium screens */
                fixed inset-y-0 right-0 w-84 sm:w-96 shadow-2xl xl:shadow-none
                /* Docked workstation column on desktop screens (xl and up) */
                xl:static xl:w-80 2xl:w-96 xl:h-auto
            `}
        >
            {/* Header */}
            <div className="flex h-14 items-center justify-between border-b border-neutral-200 px-4 dark:border-neutral-800 shrink-0">
                <div className="flex items-center gap-2 overflow-hidden">
                    <div className="flex size-6 shrink-0 items-center justify-center rounded-xs bg-black text-white dark:bg-white dark:text-black">
                        {selectedSpecimen ? <Layers className="size-3.5" /> : <Activity className="size-3.5" />}
                    </div>
                    <div className="min-w-0">
                        <h2 className="text-xs font-bold uppercase tracking-wider text-neutral-950 dark:text-neutral-50 truncate">
                            {selectedSpecimen ? 'Specimen Inspector' : 'Curatorial Workstation'}
                        </h2>
                        <span className="text-[10px] font-mono text-neutral-500 dark:text-neutral-400 block truncate">
                            {selectedSpecimen ? selectedSpecimen.specimen_code : 'Live Operations & Telemetry'}
                        </span>
                    </div>
                </div>
                <div className="flex items-center gap-1 shrink-0">
                    {selectedSpecimen && onClearSelectedSpecimen && (
                        <button
                            onClick={onClearSelectedSpecimen}
                            title="Back to workstation overview"
                            className="px-2 py-1 text-[10px] font-mono uppercase text-neutral-500 hover:text-black dark:hover:text-white rounded-xs hover:bg-neutral-100 dark:hover:bg-neutral-900"
                        >
                            Summary
                        </button>
                    )}
                    <button
                        onClick={onClose}
                        title="Collapse side panel"
                        className="flex size-7 items-center justify-center rounded-xs text-neutral-500 hover:bg-neutral-100 hover:text-black dark:text-neutral-400 dark:hover:bg-neutral-900 dark:hover:text-white"
                    >
                        <PanelRightClose className="size-4" />
                    </button>
                </div>
            </div>

            {/* Panel Body */}
            <div className="flex-1 overflow-y-auto p-4 space-y-5 scrollbar-none">
                {selectedSpecimen ? (
                    /* ------------------------------------------------------------------------- */
                    /* SPECIMEN INSPECTOR VIEW (WHEN AN ITEM IS SELECTED) */
                    /* ------------------------------------------------------------------------- */
                    <>
                        {/* Status & Code Hero */}
                        <div className="rounded-xs border border-neutral-200 p-3.5 bg-neutral-50 dark:border-neutral-800 dark:bg-neutral-900/50 space-y-2">
                            <div className="flex items-center justify-between">
                                <span className="font-mono text-sm font-bold text-neutral-950 dark:text-white">
                                    {selectedSpecimen.specimen_code}
                                </span>
                                <span className={`rounded-xs px-2 py-0.5 text-[9px] font-mono font-bold uppercase tracking-wider ${
                                    selectedSpecimen.status === 'active'
                                        ? 'bg-neutral-950 text-white dark:bg-white dark:text-black'
                                        : selectedSpecimen.status === 'loaned'
                                        ? 'border border-neutral-950 text-neutral-950 dark:border-white dark:text-white'
                                        : 'bg-neutral-200 text-neutral-800 dark:bg-neutral-800 dark:text-neutral-200'
                                }`}>
                                    {selectedSpecimen.status}
                                </span>
                            </div>
                            <div className="flex items-center justify-between text-[10px] font-mono text-neutral-500 dark:text-neutral-400">
                                <span>Catalog: {selectedSpecimen.catalog_number || 'N/A'}</span>
                                {selectedSpecimen.id && <span>ID: ...{selectedSpecimen.id.slice(-6)}</span>}
                            </div>
                        </div>

                        {/* Taxon Identity */}
                        <div className="space-y-1.5 border-b border-neutral-200 pb-4 dark:border-neutral-800">
                            <span className="text-[10px] font-mono uppercase tracking-wider text-neutral-400">Taxonomic Systematics</span>
                            <h3 className="text-sm font-serif italic font-bold text-neutral-950 dark:text-neutral-50">
                                {selectedSpecimen.scientific_name}
                            </h3>
                            {selectedSpecimen.common_name && (
                                <p className="text-xs text-neutral-600 dark:text-neutral-400">
                                    Vernacular: <span className="font-medium text-neutral-900 dark:text-neutral-200">{selectedSpecimen.common_name}</span>
                                </p>
                            )}

                            {selectedSpecimen.taxon && (
                                <div className="mt-2 text-[10px] font-mono grid grid-cols-2 gap-1.5 p-2 rounded-xs bg-neutral-100 dark:bg-neutral-900 text-neutral-700 dark:text-neutral-300">
                                    <div><span className="text-neutral-400">FAMILY:</span> {selectedSpecimen.taxon.family || '—'}</div>
                                    <div><span className="text-neutral-400">ORDER:</span> {selectedSpecimen.taxon.order || '—'}</div>
                                    <div><span className="text-neutral-400">CLASS:</span> {selectedSpecimen.taxon.class || '—'}</div>
                                    <div><span className="text-neutral-400">PHYLUM:</span> {selectedSpecimen.taxon.phylum || '—'}</div>
                                </div>
                            )}
                        </div>

                        {/* Physical Preservation & Storage */}
                        <div className="space-y-2 border-b border-neutral-200 pb-4 dark:border-neutral-800 text-xs">
                            <span className="text-[10px] font-mono uppercase tracking-wider text-neutral-400">Preservation & Vault</span>
                            <div className="grid grid-cols-2 gap-2">
                                <div className="p-2 border border-neutral-200 dark:border-neutral-800 rounded-xs">
                                    <span className="text-[9px] font-mono uppercase text-neutral-400 block">Method</span>
                                    <span className="font-mono font-bold uppercase text-neutral-900 dark:text-neutral-100">
                                        {selectedSpecimen.preservation_method || 'N/A'}
                                    </span>
                                </div>
                                <div className="p-2 border border-neutral-200 dark:border-neutral-800 rounded-xs">
                                    <span className="text-[9px] font-mono uppercase text-neutral-400 block">Storage Unit</span>
                                    <span className="font-mono font-medium text-neutral-900 dark:text-neutral-100 truncate block">
                                        {selectedSpecimen.storage_location || 'Standard Archive'}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {/* Locality Coordinates */}
                        <div className="space-y-2 border-b border-neutral-200 pb-4 dark:border-neutral-800 text-xs">
                            <span className="text-[10px] font-mono uppercase tracking-wider text-neutral-400">Geographic Locality</span>
                            <div className="flex items-start gap-2 text-neutral-700 dark:text-neutral-300">
                                <MapPin className="size-4 shrink-0 text-neutral-400 mt-0.5" />
                                <div>
                                    <p className="font-medium text-neutral-900 dark:text-neutral-100">
                                        {selectedSpecimen.location?.name || selectedSpecimen.collection_site || 'Unspecified site'}
                                    </p>
                                    {selectedSpecimen.location && (
                                        <p className="font-mono text-[10px] text-neutral-500 mt-0.5">
                                            {selectedSpecimen.location.latitude}, {selectedSpecimen.location.longitude}
                                        </p>
                                    )}
                                    {selectedSpecimen.collection_date && (
                                        <p className="font-mono text-[10px] text-neutral-400 mt-0.5">
                                            Collected: {new Date(selectedSpecimen.collection_date).toLocaleDateString()}
                                        </p>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Associated Collection & Project */}
                        <div className="space-y-2 border-b border-neutral-200 pb-4 dark:border-neutral-800 text-xs">
                            <span className="text-[10px] font-mono uppercase tracking-wider text-neutral-400">Repository Curation</span>
                            <div className="p-2.5 rounded-xs bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 space-y-1">
                                <p className="font-medium text-neutral-900 dark:text-neutral-100">
                                    {selectedSpecimen.collection?.name || 'General Repository Archive'}
                                </p>
                                {selectedSpecimen.project && (
                                    <p className="text-[10px] font-mono text-neutral-500">
                                        Project: {selectedSpecimen.project.project_code} ({selectedSpecimen.project.name})
                                    </p>
                                )}
                            </div>
                        </div>

                        {/* Actions */}
                        <div className="space-y-2 pt-1">
                            <button
                                onClick={() => onInspectModal(selectedSpecimen)}
                                className="w-full flex items-center justify-center gap-2 py-2 text-xs font-medium bg-black text-white hover:bg-neutral-800 rounded-xs dark:bg-white dark:text-black dark:hover:bg-neutral-200 transition-colors"
                            >
                                <Eye className="size-3.5" />
                                <span>Deep Inspector Modal</span>
                            </button>
                            <div className="grid grid-cols-2 gap-2">
                                <button
                                    onClick={() => onEditSpecimen(selectedSpecimen)}
                                    className="flex items-center justify-center gap-1.5 py-1.5 text-xs font-medium border border-neutral-300 hover:bg-neutral-100 rounded-xs dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-900 transition-colors"
                                >
                                    <Edit3 className="size-3" />
                                    <span>Edit Voucher</span>
                                </button>
                                <button
                                    onClick={() => onCreateLoanForSpecimen(selectedSpecimen)}
                                    className="flex items-center justify-center gap-1.5 py-1.5 text-xs font-medium border border-neutral-300 hover:bg-neutral-100 rounded-xs dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-900 transition-colors"
                                >
                                    <GitPullRequest className="size-3" />
                                    <span>Issue Loan</span>
                                </button>
                            </div>
                        </div>
                    </>
                ) : (
                    /* ------------------------------------------------------------------------- */
                    /* WORKSTATION OVERVIEW / TELEMETRY VIEW (DEFAULT) */
                    /* ------------------------------------------------------------------------- */
                    <div className="space-y-5">
                        {/* 1. Quick Curatorial Actions */}
                        <div className="space-y-2">
                            <div className="flex items-center justify-between">
                                <span className="text-[10px] font-mono uppercase tracking-wider text-neutral-400">
                                    Quick Curatorial Ops
                                </span>
                                <span className="text-[10px] font-mono text-neutral-400">1-Click</span>
                            </div>
                            <div className="grid grid-cols-2 gap-2">
                                {onOpenRegisterSpecimen && (
                                    <button
                                        onClick={onOpenRegisterSpecimen}
                                        className="flex items-center justify-center gap-1.5 py-2 px-3 text-xs font-medium bg-black text-white hover:bg-neutral-800 rounded-xs dark:bg-white dark:text-black dark:hover:bg-neutral-200 transition-colors"
                                    >
                                        <Plus className="size-3.5" />
                                        <span>+ Specimen</span>
                                    </button>
                                )}
                                {onOpenIssueLoan && (
                                    <button
                                        onClick={onOpenIssueLoan}
                                        className="flex items-center justify-center gap-1.5 py-2 px-3 text-xs font-medium border border-neutral-300 hover:bg-neutral-100 text-neutral-900 rounded-xs dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-900 transition-colors"
                                    >
                                        <GitPullRequest className="size-3.5" />
                                        <span>+ Loan</span>
                                    </button>
                                )}
                            </div>
                        </div>

                        {/* 2. Immediate Action Required: Overdue Loans Feed */}
                        <div className="space-y-2">
                            <div className="flex items-center justify-between">
                                <span className="text-[10px] font-mono uppercase tracking-wider text-neutral-400 flex items-center gap-1">
                                    <AlertTriangle className="size-3" />
                                    <span>Action Required ({overdueLoans.length})</span>
                                </span>
                                <span className="text-[10px] font-mono text-neutral-400">Overdue</span>
                            </div>

                            {overdueLoans.length > 0 ? (
                                <div className="space-y-2">
                                    {overdueLoans.slice(0, 3).map((loan) => (
                                        <div 
                                            key={loan.id}
                                            className="p-2.5 rounded-xs border border-neutral-900 bg-black text-white dark:border-white dark:bg-white dark:text-black space-y-1.5"
                                        >
                                            <div className="flex items-center justify-between">
                                                <span className="font-mono text-xs font-bold truncate">
                                                    {loan.specimen?.specimen_code || 'VOUCHER'}
                                                </span>
                                                <span className="text-[9px] font-mono uppercase bg-neutral-800 text-white dark:bg-neutral-200 dark:text-black px-1.5 py-0.5 rounded-xs">
                                                    Overdue
                                                </span>
                                            </div>
                                            <p className="text-[11px] truncate opacity-80">
                                                Borrower: {loan.borrower_name}
                                            </p>
                                            <div className="flex items-center justify-between text-[10px] font-mono pt-1">
                                                <span className="opacity-70">Due: {loan.expected_return_date}</span>
                                                {onRecordReturn && (
                                                    <button
                                                        onClick={() => onRecordReturn(loan.id)}
                                                        className="px-2 py-0.5 rounded-xs font-bold border border-white dark:border-black hover:opacity-80"
                                                    >
                                                        Return
                                                    </button>
                                                )}
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="p-3 rounded-xs border border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-900 text-xs text-neutral-500 text-center">
                                    All approved loans are within return windows.
                                </div>
                            )}
                        </div>

                        {/* 3. Physical Preservation Bank Distribution */}
                        <div className="space-y-2 border-t border-neutral-200 pt-4 dark:border-neutral-800">
                            <div className="flex items-center justify-between">
                                <span className="text-[10px] font-mono uppercase tracking-wider text-neutral-400">
                                    Preservation Vault Distribution
                                </span>
                                <span className="text-[10px] font-mono text-neutral-500">
                                    {metrics?.specimens?.total || 114} Vouchers
                                </span>
                            </div>
                            <div className="space-y-2 text-xs">
                                {preservationCounts.map((p) => (
                                    <div key={p.label} className="space-y-1">
                                        <div className="flex justify-between text-neutral-600 dark:text-neutral-400 text-[11px]">
                                            <span>{p.label}</span>
                                            <span className="font-mono">{p.count} ({p.percent}%)</span>
                                        </div>
                                        <div className="h-1.5 w-full rounded-xs bg-neutral-100 overflow-hidden dark:bg-neutral-800">
                                            <div 
                                                className="h-full bg-neutral-900 dark:bg-neutral-100" 
                                                style={{ width: `${p.percent}%` }}
                                            />
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>

                        {/* 4. Real-time Security Audit Stream */}
                        <div className="space-y-2 border-t border-neutral-200 pt-4 dark:border-neutral-800">
                            <div className="flex items-center justify-between">
                                <span className="text-[10px] font-mono uppercase tracking-wider text-neutral-400 flex items-center gap-1">
                                    <ShieldCheck className="size-3" />
                                    <span>Security Audit Stream</span>
                                </span>
                                <span className="text-[10px] font-mono text-neutral-500">Live</span>
                            </div>
                            <div className="space-y-2">
                                {auditLogs.slice(0, 3).map((log) => (
                                    <div key={log.id} className="p-2 rounded-xs border border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-900/50 space-y-1 text-xs">
                                        <div className="flex items-center justify-between">
                                            <span className="font-mono font-bold text-[10px] uppercase text-neutral-950 dark:text-white">
                                                {log.action}
                                            </span>
                                            <span className="text-[9px] font-mono text-neutral-400 flex items-center gap-1">
                                                <Clock className="size-2.5" />
                                                {log.created_at ? new Date(log.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : 'Now'}
                                            </span>
                                        </div>
                                        <p className="text-[11px] text-neutral-600 dark:text-neutral-400 truncate">
                                            {log.description}
                                        </p>
                                    </div>
                                ))}
                            </div>
                        </div>

                        {/* 5. Database & API Telemetry Card */}
                        <div className="border-t border-neutral-200 pt-4 dark:border-neutral-800">
                            <div className="rounded-xs border border-neutral-200 p-3 bg-neutral-50 dark:border-neutral-800 dark:bg-neutral-900/50 space-y-2 text-xs">
                                <div className="flex items-center justify-between">
                                    <span className="font-mono text-[10px] uppercase text-neutral-400">Database Engine</span>
                                    <span className="font-mono text-[10px] font-bold text-neutral-950 dark:text-white flex items-center gap-1">
                                        <span className="size-1.5 rounded-full bg-neutral-950 dark:bg-white animate-pulse" />
                                        MongoDB 7.0
                                    </span>
                                </div>
                                <div className="flex items-center justify-between text-[10px] font-mono text-neutral-500">
                                    <span>Host Connection</span>
                                    <span>127.0.0.1:27018</span>
                                </div>
                                <div className="flex items-center justify-between text-[10px] font-mono text-neutral-500">
                                    <span>REST API Endpoints</span>
                                    <span>52 Registered</span>
                                </div>
                                <div className="pt-1">
                                    <a
                                        href="/api/documentation"
                                        target="_blank"
                                        rel="noreferrer"
                                        className="w-full flex items-center justify-between py-1 px-2 text-[11px] font-medium border border-neutral-300 rounded-xs hover:bg-neutral-100 dark:border-neutral-700 dark:hover:bg-neutral-800 text-neutral-800 dark:text-neutral-200 transition-colors"
                                    >
                                        <span>OpenAPI 3.0 Documentation</span>
                                        <ExternalLink className="size-3" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </aside>
    );
};
