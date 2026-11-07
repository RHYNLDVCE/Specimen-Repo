import React from 'react';
import { X, MapPin, Calendar, User, FileText, Image as ImageIcon, Box, Edit3, Trash2, GitPullRequest } from 'lucide-react';

interface SpecimenDetailModalProps {
    isOpen: boolean;
    onClose: () => void;
    specimen: any | null;
    onEdit: (specimen: any) => void;
    onDelete: (specimen: any) => void;
    onRequestLoan: (specimen: any) => void;
}

export const SpecimenDetailModal: React.FC<SpecimenDetailModalProps> = ({
    isOpen,
    onClose,
    specimen,
    onEdit,
    onDelete,
    onRequestLoan,
}) => {
    if (!isOpen || !specimen) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs">
            <div className="flex max-h-[90vh] w-full max-w-3xl flex-col rounded-md border border-neutral-300 bg-white shadow-xl dark:border-neutral-800 dark:bg-neutral-900">
                {/* Header */}
                <div className="flex items-start justify-between border-b border-neutral-200 px-6 py-4 dark:border-neutral-800">
                    <div>
                        <div className="flex items-center gap-2 mb-1">
                            <span className="font-mono text-sm font-semibold tracking-tight text-neutral-950 dark:text-neutral-50">
                                {specimen.specimen_code}
                            </span>
                            {specimen.catalog_number && (
                                <span className="rounded-sm border border-neutral-300 px-1.5 py-0.5 text-[10px] font-mono text-neutral-600 dark:border-neutral-700 dark:text-neutral-400">
                                    {specimen.catalog_number}
                                </span>
                            )}
                            <span className="rounded-sm border border-neutral-950 bg-neutral-950 px-2 py-0.5 text-[10px] uppercase tracking-wider text-white dark:border-neutral-100 dark:bg-neutral-100 dark:text-neutral-950 font-bold">
                                {specimen.status}
                            </span>
                        </div>
                        <h2 className="text-lg font-bold italic text-neutral-950 dark:text-neutral-50">
                            {specimen.scientific_name}
                        </h2>
                        {specimen.common_name && (
                            <p className="text-xs text-neutral-600 dark:text-neutral-400">
                                {specimen.common_name}
                            </p>
                        )}
                    </div>
                    <button
                        onClick={onClose}
                        className="rounded-sm p-1 text-neutral-500 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:bg-neutral-800"
                    >
                        <X className="size-5" />
                    </button>
                </div>

                {/* Content */}
                <div className="flex-1 overflow-y-auto p-6 space-y-6">
                    {/* Taxonomic Classification */}
                    <div>
                        <h4 className="text-xs font-mono uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-2">
                            Taxonomic Hierarchy
                        </h4>
                        <div className="grid grid-cols-2 gap-2 sm:grid-cols-4 text-xs">
                            <div className="rounded-sm border border-neutral-200 p-2 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-950">
                                <span className="block text-[10px] text-neutral-500">Kingdom</span>
                                <span className="font-medium text-neutral-900 dark:text-neutral-100">{specimen.kingdom || '—'}</span>
                            </div>
                            <div className="rounded-sm border border-neutral-200 p-2 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-950">
                                <span className="block text-[10px] text-neutral-500">Phylum</span>
                                <span className="font-medium text-neutral-900 dark:text-neutral-100">{specimen.phylum || '—'}</span>
                            </div>
                            <div className="rounded-sm border border-neutral-200 p-2 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-950">
                                <span className="block text-[10px] text-neutral-500">Class</span>
                                <span className="font-medium text-neutral-900 dark:text-neutral-100">{specimen.class || '—'}</span>
                            </div>
                            <div className="rounded-sm border border-neutral-200 p-2 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-950">
                                <span className="block text-[10px] text-neutral-500">Order</span>
                                <span className="font-medium text-neutral-900 dark:text-neutral-100">{specimen.order || '—'}</span>
                            </div>
                            <div className="rounded-sm border border-neutral-200 p-2 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-950">
                                <span className="block text-[10px] text-neutral-500">Family</span>
                                <span className="font-medium text-neutral-900 dark:text-neutral-100">{specimen.family || '—'}</span>
                            </div>
                            <div className="rounded-sm border border-neutral-200 p-2 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-950">
                                <span className="block text-[10px] text-neutral-500">Genus</span>
                                <span className="font-medium italic text-neutral-900 dark:text-neutral-100">{specimen.genus || '—'}</span>
                            </div>
                            <div className="rounded-sm border border-neutral-200 p-2 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-950 col-span-2">
                                <span className="block text-[10px] text-neutral-500">Species Epithet</span>
                                <span className="font-medium italic text-neutral-900 dark:text-neutral-100">{specimen.species || '—'}</span>
                            </div>
                        </div>
                    </div>

                    {/* Repository Storage & Preservation */}
                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div className="rounded-sm border border-neutral-200 p-4 dark:border-neutral-800">
                            <h4 className="text-xs font-mono uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-2 flex items-center gap-1.5">
                                <Box className="size-3.5" />
                                <span>Storage & Physical Method</span>
                            </h4>
                            <div className="space-y-1.5 text-xs">
                                <div className="flex justify-between">
                                    <span className="text-neutral-500">Preservation:</span>
                                    <span className="font-mono uppercase font-semibold text-neutral-900 dark:text-neutral-100">
                                        {specimen.preservation_method}
                                    </span>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-neutral-500">Repository Location:</span>
                                    <span className="font-medium text-neutral-900 dark:text-neutral-100">
                                        {specimen.repository_location || 'Unassigned'}
                                    </span>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-neutral-500">Collection Archive:</span>
                                    <span className="font-medium text-neutral-900 dark:text-neutral-100">
                                        {specimen.collection?.name || 'Independent / General'}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div className="rounded-sm border border-neutral-200 p-4 dark:border-neutral-800">
                            <h4 className="text-xs font-mono uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-2 flex items-center gap-1.5">
                                <MapPin className="size-3.5" />
                                <span>Locality & Sampling Site</span>
                            </h4>
                            <div className="space-y-1.5 text-xs">
                                <div className="flex justify-between">
                                    <span className="text-neutral-500">Site Name:</span>
                                    <span className="font-medium text-neutral-900 dark:text-neutral-100">
                                        {specimen.location?.name || 'Unspecified'}
                                    </span>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-neutral-500">Coordinates:</span>
                                    <span className="font-mono text-neutral-900 dark:text-neutral-100">
                                        {specimen.latitude && specimen.longitude
                                            ? `${specimen.latitude}°, ${specimen.longitude}°`
                                            : 'N/A'}
                                    </span>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-neutral-500">Habitat:</span>
                                    <span className="font-medium text-neutral-900 dark:text-neutral-100">
                                        {specimen.habitat || specimen.location?.habitat || 'N/A'}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Collector & Date Info */}
                    <div className="flex flex-wrap gap-4 rounded-sm border border-neutral-200 p-3 text-xs dark:border-neutral-800">
                        <div className="flex items-center gap-1.5 text-neutral-600 dark:text-neutral-400">
                            <User className="size-3.5" />
                            <span>Collector: <strong className="text-neutral-900 dark:text-neutral-100">{specimen.collector || 'Unknown'}</strong></span>
                        </div>
                        <div className="flex items-center gap-1.5 text-neutral-600 dark:text-neutral-400">
                            <Calendar className="size-3.5" />
                            <span>Collected: <strong className="text-neutral-900 dark:text-neutral-100">{specimen.collection_date || 'N/A'}</strong></span>
                        </div>
                    </div>

                    {/* Description & Notes */}
                    {specimen.description && (
                        <div>
                            <h4 className="text-xs font-mono uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-1">
                                Field Notes & Description
                            </h4>
                            <div className="rounded-sm border border-neutral-200 p-3 text-xs leading-relaxed text-neutral-800 dark:border-neutral-800 dark:text-neutral-200 bg-neutral-50 dark:bg-neutral-950">
                                {specimen.description}
                            </div>
                        </div>
                    )}

                    {/* Attached Media / Documents */}
                    <div>
                        <h4 className="text-xs font-mono uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-2 flex items-center gap-1.5">
                            <ImageIcon className="size-3.5" />
                            <span>Voucher Documentation & Imagery</span>
                        </h4>
                        <div className="grid grid-cols-1 gap-2 sm:grid-cols-2 text-xs">
                            <div className="flex items-center gap-2.5 rounded-sm border border-neutral-200 p-2.5 dark:border-neutral-800">
                                <ImageIcon className="size-4 text-neutral-500" />
                                <div>
                                    <p className="font-medium text-neutral-900 dark:text-neutral-100">
                                        Dorsal Voucher Photograph
                                    </p>
                                    <p className="text-[10px] text-neutral-500">
                                        High-resolution calibrated color voucher photo
                                    </p>
                                </div>
                            </div>
                            <div className="flex items-center gap-2.5 rounded-sm border border-neutral-200 p-2.5 dark:border-neutral-800">
                                <FileText className="size-4 text-neutral-500" />
                                <div>
                                    <p className="font-medium text-neutral-900 dark:text-neutral-100">
                                        Field Ledger PDF Record
                                    </p>
                                    <p className="text-[10px] text-neutral-500">
                                        Original specimen accession notes
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Footer Controls */}
                <div className="flex items-center justify-between border-t border-neutral-200 px-6 py-4 dark:border-neutral-800">
                    <button
                        onClick={() => onDelete(specimen)}
                        className="flex items-center gap-1.5 text-xs text-neutral-500 hover:text-black dark:text-neutral-400 dark:hover:text-white"
                    >
                        <Trash2 className="size-3.5" />
                        <span>Deaccession / Delete</span>
                    </button>

                    <div className="flex items-center gap-2">
                        {specimen.status === 'active' && (
                            <button
                                onClick={() => {
                                    onClose();
                                    onRequestLoan(specimen);
                                }}
                                className="flex items-center gap-1.5 rounded-sm border border-neutral-300 px-3 py-1.5 text-xs font-medium text-neutral-800 hover:bg-neutral-100 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800"
                            >
                                <GitPullRequest className="size-3.5" />
                                <span>Issue Loan</span>
                            </button>
                        )}
                        <button
                            onClick={() => {
                                onClose();
                                onEdit(specimen);
                            }}
                            className="flex items-center gap-1.5 rounded-sm bg-black px-3 py-1.5 text-xs font-medium text-white hover:opacity-90 dark:bg-white dark:text-black"
                        >
                            <Edit3 className="size-3.5" />
                            <span>Edit Voucher</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
};
