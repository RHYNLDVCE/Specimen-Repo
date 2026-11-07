import React from 'react';
import { 
    Activity, 
    BookOpen, 
    Boxes, 
    Compass, 
    Database, 
    FileText, 
    GitPullRequest, 
    LogOut, 
    Moon, 
    ShieldCheck, 
    Sun, 
    Users, 
    Layers,
    PanelLeftClose,
    PanelLeftOpen,
    PanelRightClose,
    PanelRightOpen,
    Menu,
    RefreshCw
} from 'lucide-react';
import { AdminUser } from '@/lib/admin-api';

interface AdminHeaderProps {
    currentTab: string;
    onTabChange: (tab: string) => void;
    user: AdminUser | null;
    onLogout: () => void;
    isDark: boolean;
    onToggleDark: () => void;
    isSidebarCollapsed: boolean;
    onToggleSidebar: () => void;
    onOpenMobileSidebar: () => void;
    isSidePanelOpen: boolean;
    onToggleSidePanel: () => void;
    onRefresh: () => void;
    isLoading: boolean;
}

export const AdminHeader: React.FC<AdminHeaderProps> = ({
    currentTab,
    onTabChange,
    user,
    onLogout,
    isDark,
    onToggleDark,
    isSidebarCollapsed,
    onToggleSidebar,
    onOpenMobileSidebar,
    isSidePanelOpen,
    onToggleSidePanel,
    onRefresh,
    isLoading,
}) => {
    const tabTitles: Record<string, { category: string; title: string }> = {
        overview: { category: 'Console', title: 'Overview Analytics' },
        specimens: { category: 'Repository', title: 'Biological Specimen Catalog' },
        loans: { category: 'Circulation', title: 'Specimen Loans Workflow' },
        taxa: { category: 'Systematics', title: 'Taxonomic Hierarchy' },
        collections: { category: 'Archives', title: 'Natural History Collections' },
        locations: { category: 'Geography', title: 'Sampling Localities' },
        researchers: { category: 'Investigators', title: 'Scientific Directory' },
        'audit-logs': { category: 'Governance', title: 'Security Audit Trail' },
    };

    const currentMeta = tabTitles[currentTab] || { category: 'Console', title: 'Administration' };

    return (
        <header className="sticky top-0 z-30 w-full border-b border-neutral-200 bg-white/95 backdrop-blur-md dark:border-neutral-800 dark:bg-black/95">
            {/* Top Toolbar */}
            <div className="flex h-14 items-center justify-between px-3 sm:px-6">
                {/* Left: Sidebar Toggles & Breadcrumbs */}
                <div className="flex items-center gap-2 sm:gap-3">
                    {/* Mobile Hamburger */}
                    <button
                        onClick={onOpenMobileSidebar}
                        title="Open navigation menu"
                        className="flex lg:hidden size-8 items-center justify-center rounded-xs border border-neutral-200 text-neutral-700 hover:bg-neutral-100 dark:border-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-900"
                    >
                        <Menu className="size-4" />
                    </button>

                    {/* Desktop Sidebar Collapse Toggle */}
                    <button
                        onClick={onToggleSidebar}
                        title={isSidebarCollapsed ? 'Expand side panel' : 'Collapse side panel'}
                        className="hidden lg:flex size-8 items-center justify-center rounded-xs border border-neutral-200 text-neutral-700 hover:bg-neutral-100 dark:border-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-900"
                    >
                        {isSidebarCollapsed ? <PanelLeftOpen className="size-4" /> : <PanelLeftClose className="size-4" />}
                    </button>

                    {/* Breadcrumbs */}
                    <div className="flex items-center gap-2 text-xs">
                        <span className="hidden sm:inline font-mono uppercase tracking-widest text-neutral-400 dark:text-neutral-500">
                            {currentMeta.category}
                        </span>
                        <span className="hidden sm:inline text-neutral-300 dark:text-neutral-700">/</span>
                        <h2 className="font-semibold text-neutral-900 dark:text-neutral-100 truncate">
                            {currentMeta.title}
                        </h2>
                    </div>
                </div>

                {/* Right: Actions & Tools */}
                <div className="flex items-center gap-1.5 sm:gap-2">
                    {/* Refresh Button */}
                    <button
                        onClick={onRefresh}
                        disabled={isLoading}
                        title="Refresh data"
                        className="flex size-8 items-center justify-center rounded-xs border border-neutral-200 text-neutral-600 hover:bg-neutral-100 hover:text-black dark:border-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-900 dark:hover:text-white transition-colors"
                    >
                        <RefreshCw className={`size-3.5 ${isLoading ? 'animate-spin' : ''}`} />
                    </button>

                    {/* Side Panel Toggle */}
                    <button
                        onClick={onToggleSidePanel}
                        title={isSidePanelOpen ? 'Hide curatorial side panel' : 'Open curatorial side panel'}
                        className={`flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-xs border transition-colors ${
                            isSidePanelOpen
                                ? 'bg-black text-white border-black dark:bg-white dark:text-black dark:border-white'
                                : 'border-neutral-200 text-neutral-700 hover:bg-neutral-100 dark:border-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-900'
                        }`}
                    >
                        {isSidePanelOpen ? <PanelRightClose className="size-3.5" /> : <PanelRightOpen className="size-3.5" />}
                        <span className="hidden md:inline font-mono text-[11px] uppercase tracking-wider">Side Panel</span>
                    </button>

                    {/* OpenAPI docs link */}
                    <a
                        href="/api/documentation"
                        target="_blank"
                        rel="noreferrer"
                        className="hidden sm:flex items-center gap-1.5 rounded-xs border border-neutral-200 px-2.5 py-1.5 text-xs text-neutral-600 transition-colors hover:bg-neutral-100 hover:text-neutral-900 dark:border-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-900 dark:hover:text-neutral-100"
                    >
                        <FileText className="size-3.5" />
                        <span className="font-mono text-[11px] uppercase tracking-wider">Swagger</span>
                    </a>

                    {/* Dark Mode toggle */}
                    <button
                        onClick={onToggleDark}
                        title={isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode'}
                        className="flex size-8 items-center justify-center rounded-xs border border-neutral-200 text-neutral-700 transition-colors hover:bg-neutral-100 dark:border-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-900"
                    >
                        {isDark ? <Sun className="size-4" /> : <Moon className="size-4" />}
                    </button>

                    {/* Logged in User Badge */}
                    {user && (
                        <div className="flex items-center gap-2 pl-2 border-l border-neutral-200 dark:border-neutral-800">
                            <div className="text-right hidden xl:block">
                                <p className="text-xs font-medium text-neutral-900 dark:text-neutral-100 leading-none">
                                    {user.name}
                                </p>
                                <p className="text-[9px] font-mono uppercase tracking-widest text-neutral-500 dark:text-neutral-400 mt-0.5">
                                    {user.role.replace('_', ' ')}
                                </p>
                            </div>
                            <button
                                onClick={onLogout}
                                title="Sign out"
                                className="flex size-8 items-center justify-center rounded-xs border border-neutral-300 text-neutral-700 transition-colors hover:bg-black hover:text-white dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-white dark:hover:text-black"
                            >
                                <LogOut className="size-3.5" />
                            </button>
                        </div>
                    )}
                </div>
            </div>
        </header>
    );
};

