import React from 'react';
import { 
    Activity, 
    Layers, 
    GitPullRequest, 
    BookOpen, 
    Boxes, 
    Compass, 
    Users, 
    ShieldCheck, 
    Plus, 
    Database, 
    FileText, 
    LogOut, 
    Moon, 
    Sun, 
    PanelLeftClose, 
    PanelLeftOpen, 
    X,
    ExternalLink
} from 'lucide-react';
import { AdminUser } from '@/lib/admin-api';

interface AdminSidebarProps {
    currentTab: string;
    onTabChange: (tab: string) => void;
    user: AdminUser | null;
    onLogout: () => void;
    isCollapsed: boolean;
    onToggleCollapse: () => void;
    isOpenMobile: boolean;
    onCloseMobile: () => void;
    metrics: any;
    onOpenRegisterSpecimen: () => void;
    onOpenIssueLoan: () => void;
    isDark: boolean;
    onToggleDark: () => void;
}

export const AdminSidebar: React.FC<AdminSidebarProps> = ({
    currentTab,
    onTabChange,
    user,
    onLogout,
    isCollapsed,
    onToggleCollapse,
    isOpenMobile,
    onCloseMobile,
    metrics,
    onOpenRegisterSpecimen,
    onOpenIssueLoan,
    isDark,
    onToggleDark,
}) => {
    const navItems = [
        { 
            id: 'overview', 
            label: 'Overview', 
            icon: Activity, 
            badge: null,
            badgeType: 'normal' as const
        },
        { 
            id: 'specimens', 
            label: 'Specimens', 
            icon: Layers, 
            badge: metrics?.specimens?.total ?? null,
            badgeType: 'normal' as const
        },
        { 
            id: 'loans', 
            label: 'Loans Workflow', 
            icon: GitPullRequest, 
            badge: metrics?.loans?.overdue > 0 ? `${metrics.loans.overdue} OVERDUE` : (metrics?.loans?.active ? `${metrics.loans.active} ACTIVE` : null),
            badgeType: metrics?.loans?.overdue > 0 ? 'urgent' as const : 'normal' as const
        },
        { 
            id: 'taxa', 
            label: 'Taxonomy Tree', 
            icon: BookOpen, 
            badge: metrics?.totals?.taxa ?? null,
            badgeType: 'normal' as const
        },
        { 
            id: 'collections', 
            label: 'Collections', 
            icon: Boxes, 
            badge: metrics?.totals?.collections ?? null,
            badgeType: 'normal' as const
        },
        { 
            id: 'locations', 
            label: 'Localities', 
            icon: Compass, 
            badge: null,
            badgeType: 'normal' as const
        },
        { 
            id: 'researchers', 
            label: 'Researchers', 
            icon: Users, 
            badge: metrics?.totals?.researchers ?? null,
            badgeType: 'normal' as const
        },
        { 
            id: 'audit-logs', 
            label: 'Security Audit', 
            icon: ShieldCheck, 
            badge: null,
            badgeType: 'normal' as const
        },
    ];

    const handleItemClick = (id: string) => {
        onTabChange(id);
        if (isOpenMobile) {
            onCloseMobile();
        }
    };

    return (
        <>
            {/* Mobile Backdrop */}
            {isOpenMobile && (
                <div 
                    onClick={onCloseMobile}
                    className="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs lg:hidden transition-opacity"
                    aria-hidden="true"
                />
            )}

            {/* Sidebar Container */}
            <aside 
                className={`
                    fixed lg:sticky top-0 bottom-0 left-0 z-50 flex flex-col
                    border-r border-neutral-200 bg-white text-neutral-900 
                    dark:border-neutral-800 dark:bg-neutral-950 dark:text-neutral-100
                    transition-all duration-300 ease-in-out h-screen
                    ${isOpenMobile ? 'translate-x-0 w-64 shadow-2xl' : '-translate-x-full lg:translate-x-0'}
                    ${isCollapsed ? 'lg:w-16' : 'lg:w-64'}
                `}
            >
                {/* Brand Header */}
                <div className="flex h-14 items-center justify-between border-b border-neutral-200 px-3.5 dark:border-neutral-800">
                    <div className="flex items-center gap-3 overflow-hidden">
                        <div className="flex size-8 shrink-0 items-center justify-center rounded-xs bg-black text-white dark:bg-white dark:text-black">
                            <Database className="size-4" />
                        </div>
                        {!isCollapsed && (
                            <div className="min-w-0 flex-1">
                                <h1 className="truncate text-xs font-bold uppercase tracking-wider text-neutral-950 dark:text-neutral-50">
                                    Specimen Repo
                                </h1>
                                <p className="truncate text-[10px] font-mono uppercase tracking-widest text-neutral-500 dark:text-neutral-400">
                                    Curatorial Console
                                </p>
                            </div>
                        )}
                    </div>

                    {/* Desktop Collapse Toggle */}
                    <button
                        onClick={onToggleCollapse}
                        title={isCollapsed ? 'Expand side panel' : 'Collapse side panel'}
                        className="hidden lg:flex size-7 items-center justify-center rounded-xs text-neutral-500 hover:bg-neutral-100 hover:text-black dark:text-neutral-400 dark:hover:bg-neutral-900 dark:hover:text-white"
                    >
                        {isCollapsed ? <PanelLeftOpen className="size-4" /> : <PanelLeftClose className="size-4" />}
                    </button>

                    {/* Mobile Close Button */}
                    <button
                        onClick={onCloseMobile}
                        className="flex lg:hidden size-7 items-center justify-center rounded-xs text-neutral-500 hover:bg-neutral-100 hover:text-black dark:text-neutral-400 dark:hover:bg-neutral-900 dark:hover:text-white"
                    >
                        <X className="size-4" />
                    </button>
                </div>

                {/* Quick Curatorial Actions (When Expanded) */}
                {!isCollapsed && (
                    <div className="p-3 border-b border-neutral-200 dark:border-neutral-800 space-y-1.5 bg-neutral-50/50 dark:bg-neutral-900/30">
                        <p className="px-1 text-[9px] font-mono uppercase tracking-widest text-neutral-400 dark:text-neutral-500">
                            Quick Operations
                        </p>
                        <div className="grid grid-cols-2 gap-1.5">
                            <button
                                onClick={onOpenRegisterSpecimen}
                                className="flex items-center justify-center gap-1 py-1.5 px-2 text-[11px] font-medium bg-black text-white hover:bg-neutral-800 rounded-xs dark:bg-white dark:text-black dark:hover:bg-neutral-200 transition-colors"
                            >
                                <Plus className="size-3" />
                                <span>Specimen</span>
                            </button>
                            <button
                                onClick={onOpenIssueLoan}
                                className="flex items-center justify-center gap-1 py-1.5 px-2 text-[11px] font-medium border border-neutral-300 hover:bg-neutral-100 text-neutral-900 rounded-xs dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-900 transition-colors"
                            >
                                <Plus className="size-3" />
                                <span>Loan</span>
                            </button>
                        </div>
                    </div>
                )}

                {/* Primary Navigation List */}
                <div className="flex-1 overflow-y-auto px-2 py-3 space-y-1 scrollbar-none">
                    {!isCollapsed && (
                        <p className="px-2 pb-1 text-[9px] font-mono uppercase tracking-widest text-neutral-400 dark:text-neutral-500">
                            Curatorial Modules
                        </p>
                    )}
                    {navItems.map((item) => {
                        const Icon = item.icon;
                        const isActive = currentTab === item.id;
                        return (
                            <button
                                key={item.id}
                                onClick={() => handleItemClick(item.id)}
                                title={isCollapsed ? item.label : undefined}
                                className={`
                                    w-full flex items-center gap-3 rounded-xs px-2.5 py-2 text-xs font-medium transition-all group
                                    ${isActive 
                                        ? 'bg-neutral-950 text-white dark:bg-white dark:text-neutral-950 shadow-xs' 
                                        : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-950 dark:text-neutral-400 dark:hover:bg-neutral-900 dark:hover:text-neutral-100'
                                    }
                                    ${isCollapsed ? 'justify-center px-0' : ''}
                                `}
                            >
                                <Icon className={`size-4 shrink-0 ${isActive ? 'text-white dark:text-neutral-950' : 'text-neutral-500 group-hover:text-neutral-900 dark:text-neutral-400 dark:group-hover:text-neutral-100'}`} />
                                {!isCollapsed && (
                                    <span className="flex-1 text-left truncate">{item.label}</span>
                                )}
                                {!isCollapsed && item.badge !== null && (
                                    <span 
                                        className={`
                                            rounded-xs px-1.5 py-0.5 text-[9px] font-mono font-bold uppercase tracking-wider
                                            ${item.badgeType === 'urgent'
                                                ? (isActive ? 'bg-white text-black dark:bg-black dark:text-white' : 'bg-black text-white dark:bg-white dark:text-black')
                                                : (isActive ? 'bg-neutral-800 text-neutral-200 dark:bg-neutral-200 dark:text-neutral-800' : 'bg-neutral-100 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300')
                                            }
                                        `}
                                    >
                                        {item.badge}
                                    </span>
                                )}
                            </button>
                        );
                    })}

                    {/* External Docs Link */}
                    <div className="pt-3">
                        {!isCollapsed && (
                            <p className="px-2 pb-1 text-[9px] font-mono uppercase tracking-widest text-neutral-400 dark:text-neutral-500">
                                System References
                            </p>
                        )}
                        <a
                            href="/api/documentation"
                            target="_blank"
                            rel="noreferrer"
                            title={isCollapsed ? 'Swagger API Docs' : undefined}
                            className={`
                                w-full flex items-center gap-3 rounded-xs px-2.5 py-2 text-xs font-medium text-neutral-600 hover:bg-neutral-100 hover:text-neutral-950 dark:text-neutral-400 dark:hover:bg-neutral-900 dark:hover:text-neutral-100 transition-colors
                                ${isCollapsed ? 'justify-center px-0' : ''}
                            `}
                        >
                            <FileText className="size-4 shrink-0 text-neutral-500 dark:text-neutral-400" />
                            {!isCollapsed && (
                                <>
                                    <span className="flex-1 text-left truncate">OpenAPI / Swagger</span>
                                    <ExternalLink className="size-3 text-neutral-400" />
                                </>
                            )}
                        </a>
                    </div>
                </div>

                {/* System Status & Database Telemetry (When expanded) */}
                {!isCollapsed && (
                    <div className="mx-2 mb-2 p-2.5 border border-neutral-200 dark:border-neutral-800 rounded-xs bg-neutral-50 dark:bg-neutral-900/50 text-[11px] space-y-1">
                        <div className="flex items-center justify-between">
                            <span className="font-mono text-[10px] uppercase text-neutral-500">Database</span>
                            <span className="flex items-center gap-1 font-mono text-[10px] font-semibold text-neutral-900 dark:text-neutral-100">
                                <span className="size-1.5 rounded-full bg-neutral-950 dark:bg-white animate-pulse" />
                                MongoDB 7
                            </span>
                        </div>
                        <div className="flex items-center justify-between text-[10px] text-neutral-500 dark:text-neutral-400 font-mono">
                            <span>Host Port</span>
                            <span>127.0.0.1:27018</span>
                        </div>
                        <div className="flex items-center justify-between text-[10px] text-neutral-500 dark:text-neutral-400 font-mono">
                            <span>REST API</span>
                            <span>v1 (52 Routes)</span>
                        </div>
                    </div>
                )}

                {/* User & Mode Controls Footer */}
                <div className="border-t border-neutral-200 p-2.5 dark:border-neutral-800 bg-white dark:bg-neutral-950">
                    <div className="flex items-center justify-between gap-2">
                        {/* Profile Info */}
                        <div className="flex items-center gap-2 overflow-hidden min-w-0">
                            <div className="flex size-7 shrink-0 items-center justify-center rounded-xs bg-neutral-200 text-neutral-900 font-bold font-mono text-xs dark:bg-neutral-800 dark:text-neutral-100">
                                {user ? user.name.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase() : 'AD'}
                            </div>
                            {!isCollapsed && user && (
                                <div className="min-w-0 flex-1">
                                    <p className="truncate text-xs font-semibold text-neutral-900 dark:text-neutral-100">
                                        {user.name}
                                    </p>
                                    <p className="truncate text-[9px] font-mono uppercase tracking-widest text-neutral-500 dark:text-neutral-400">
                                        {user.role.replace('_', ' ')}
                                    </p>
                                </div>
                            )}
                        </div>

                        {/* Theme & Logout Buttons */}
                        <div className="flex items-center gap-1 shrink-0">
                            <button
                                onClick={onToggleDark}
                                title={isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode'}
                                className="flex size-7 items-center justify-center rounded-xs text-neutral-600 hover:bg-neutral-100 hover:text-black dark:text-neutral-400 dark:hover:bg-neutral-900 dark:hover:text-white"
                            >
                                {isDark ? <Sun className="size-3.5" /> : <Moon className="size-3.5" />}
                            </button>
                            {user && (
                                <button
                                    onClick={onLogout}
                                    title="Sign out of console"
                                    className="flex size-7 items-center justify-center rounded-xs text-neutral-600 hover:bg-black hover:text-white dark:text-neutral-400 dark:hover:bg-white dark:hover:text-black transition-colors"
                                >
                                    <LogOut className="size-3.5" />
                                </button>
                            )}
                        </div>
                    </div>
                </div>
            </aside>
        </>
    );
};
