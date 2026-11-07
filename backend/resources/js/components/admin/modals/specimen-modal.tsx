import React, { useState, useEffect } from 'react';
import { X, Save, AlertCircle } from 'lucide-react';
import { adminApi } from '@/lib/admin-api';

interface SpecimenModalProps {
    isOpen: boolean;
    onClose: () => void;
    onSaved: () => void;
    specimenToEdit?: any | null;
    taxa: any[];
    collections: any[];
    locations: any[];
    projects: any[];
}

export const SpecimenModal: React.FC<SpecimenModalProps> = ({
    isOpen,
    onClose,
    onSaved,
    specimenToEdit,
    taxa,
    collections,
    locations,
    projects,
}) => {
    const isEdit = Boolean(specimenToEdit);

    const [formData, setFormData] = useState({
        specimen_code: '',
        catalog_number: '',
        scientific_name: '',
        common_name: '',
        kingdom: 'Animalia',
        phylum: '',
        class: '',
        order: '',
        family: '',
        genus: '',
        species: '',
        preservation_method: 'wet',
        repository_location: '',
        status: 'active',
        taxon_id: '',
        collection_id: '',
        location_id: '',
        project_id: '',
        collector: '',
        collection_date: new Date().toISOString().split('T')[0],
        description: '',
    });

    const [isSaving, setIsSaving] = useState(false);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);

    useEffect(() => {
        if (specimenToEdit) {
            setFormData({
                specimen_code: specimenToEdit.specimen_code || '',
                catalog_number: specimenToEdit.catalog_number || '',
                scientific_name: specimenToEdit.scientific_name || '',
                common_name: specimenToEdit.common_name || '',
                kingdom: specimenToEdit.kingdom || 'Animalia',
                phylum: specimenToEdit.phylum || '',
                class: specimenToEdit.class || '',
                order: specimenToEdit.order || '',
                family: specimenToEdit.family || '',
                genus: specimenToEdit.genus || '',
                species: specimenToEdit.species || '',
                preservation_method: specimenToEdit.preservation_method || 'wet',
                repository_location: specimenToEdit.repository_location || '',
                status: specimenToEdit.status || 'active',
                taxon_id: specimenToEdit.taxon_id || '',
                collection_id: specimenToEdit.collection_id || '',
                location_id: specimenToEdit.location_id || '',
                project_id: specimenToEdit.project_id || '',
                collector: specimenToEdit.collector || '',
                collection_date: specimenToEdit.collection_date || new Date().toISOString().split('T')[0],
                description: specimenToEdit.description || '',
            });
        } else {
            const year = new Date().getFullYear();
            setFormData({
                specimen_code: `SPEC-${year}-VOUCH-${Math.floor(1000 + Math.random() * 9000)}`,
                catalog_number: `NM-BIO-${year}-${Math.floor(1000 + Math.random() * 9000)}`,
                scientific_name: '',
                common_name: '',
                kingdom: 'Animalia',
                phylum: '',
                class: '',
                order: '',
                family: '',
                genus: '',
                species: '',
                preservation_method: 'wet',
                repository_location: 'Cabinet A-01, Shelf 01',
                status: 'active',
                taxon_id: '',
                collection_id: '',
                location_id: '',
                project_id: '',
                collector: 'Rhayan Lodovice',
                collection_date: new Date().toISOString().split('T')[0],
                description: '',
            });
        }
        setErrorMessage(null);
    }, [specimenToEdit, isOpen]);

    // Auto-fill taxonomy fields when taxon is selected
    const handleTaxonChange = (taxonId: string) => {
        const found = taxa.find((t) => t.id === taxonId);
        if (found) {
            setFormData((prev) => ({
                ...prev,
                taxon_id: taxonId,
                scientific_name: found.scientific_name || prev.scientific_name,
                common_name: found.common_name || prev.common_name,
                kingdom: found.kingdom || prev.kingdom,
                phylum: found.phylum || prev.phylum,
                class: found.class || prev.class,
                order: found.order || prev.order,
                family: found.family || prev.family,
                genus: found.genus || prev.genus,
                species: found.species || prev.species,
            }));
        } else {
            setFormData((prev) => ({ ...prev, taxon_id: taxonId }));
        }
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setIsSaving(true);
        setErrorMessage(null);

        try {
            const payload: any = { ...formData };
            if (!payload.taxon_id) delete payload.taxon_id;
            if (!payload.collection_id) delete payload.collection_id;
            if (!payload.location_id) delete payload.location_id;
            if (!payload.project_id) delete payload.project_id;

            let res;
            if (isEdit) {
                res = await adminApi.updateSpecimen(specimenToEdit.id, payload);
            } else {
                res = await adminApi.createSpecimen(payload);
            }

            if (res.success) {
                onSaved();
                onClose();
            } else {
                setErrorMessage(res.message || 'Operation failed. Verify validation inputs.');
            }
        } catch {
            setErrorMessage('Network or server error occurred.');
        } finally {
            setIsSaving(false);
        }
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs">
            <div className="flex max-h-[90vh] w-full max-w-2xl flex-col rounded-md border border-neutral-300 bg-white shadow-xl dark:border-neutral-800 dark:bg-neutral-900">
                {/* Modal Header */}
                <div className="flex items-center justify-between border-b border-neutral-200 px-6 py-4 dark:border-neutral-800">
                    <div>
                        <h3 className="text-base font-semibold text-neutral-950 dark:text-neutral-50">
                            {isEdit ? 'Edit Biological Specimen' : 'Register New Specimen Voucher'}
                        </h3>
                        <p className="text-xs text-neutral-500 dark:text-neutral-400">
                            Enter taxonomic, geographical, and repository storage parameters.
                        </p>
                    </div>
                    <button
                        onClick={onClose}
                        className="rounded-sm p-1 text-neutral-500 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:bg-neutral-800"
                    >
                        <X className="size-5" />
                    </button>
                </div>

                {/* Modal Body */}
                <form onSubmit={handleSubmit} className="flex-1 overflow-y-auto p-6 space-y-4">
                    {errorMessage && (
                        <div className="flex items-start gap-2 rounded-sm border border-neutral-400 bg-neutral-100 p-3 text-xs text-neutral-900 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200">
                            <AlertCircle className="size-4 shrink-0 mt-0.5 text-neutral-700 dark:text-neutral-300" />
                            <span>{errorMessage}</span>
                        </div>
                    )}

                    {/* Codes row */}
                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label className="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Specimen Code *
                            </label>
                            <input
                                type="text"
                                required
                                value={formData.specimen_code}
                                onChange={(e) => setFormData({ ...formData, specimen_code: e.target.value })}
                                className="w-full rounded-sm border border-neutral-300 bg-transparent px-3 py-1.5 text-xs text-neutral-900 font-mono dark:border-neutral-700 dark:text-neutral-100"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Catalog Number
                            </label>
                            <input
                                type="text"
                                value={formData.catalog_number}
                                onChange={(e) => setFormData({ ...formData, catalog_number: e.target.value })}
                                className="w-full rounded-sm border border-neutral-300 bg-transparent px-3 py-1.5 text-xs text-neutral-900 font-mono dark:border-neutral-700 dark:text-neutral-100"
                            />
                        </div>
                    </div>

                    {/* Taxon Link Dropdown */}
                    <div>
                        <label className="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                            Link Existing Taxon (Auto-fills Scientific Name & Hierarchy)
                        </label>
                        <select
                            value={formData.taxon_id}
                            onChange={(e) => handleTaxonChange(e.target.value)}
                            className="w-full rounded-sm border border-neutral-300 bg-transparent px-3 py-1.5 text-xs text-neutral-900 dark:border-neutral-700 dark:text-neutral-100 dark:bg-neutral-900"
                        >
                            <option value="">-- Manual Taxonomy or Select Below --</option>
                            {taxa.map((t) => (
                                <option key={t.id} value={t.id}>
                                    {t.scientific_name} ({t.common_name || t.family || 'Taxon'})
                                </option>
                            ))}
                        </select>
                    </div>

                    {/* Names row */}
                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label className="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Scientific Name *
                            </label>
                            <input
                                type="text"
                                required
                                value={formData.scientific_name}
                                onChange={(e) => setFormData({ ...formData, scientific_name: e.target.value })}
                                placeholder="e.g. Pithecophaga jefferyi"
                                className="w-full rounded-sm border border-neutral-300 bg-transparent px-3 py-1.5 text-xs text-neutral-900 italic dark:border-neutral-700 dark:text-neutral-100"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Common Name
                            </label>
                            <input
                                type="text"
                                value={formData.common_name}
                                onChange={(e) => setFormData({ ...formData, common_name: e.target.value })}
                                placeholder="e.g. Philippine Eagle"
                                className="w-full rounded-sm border border-neutral-300 bg-transparent px-3 py-1.5 text-xs text-neutral-900 dark:border-neutral-700 dark:text-neutral-100"
                            />
                        </div>
                    </div>

                    {/* Preservation, Storage & Status */}
                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <label className="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Preservation Method *
                            </label>
                            <select
                                value={formData.preservation_method}
                                onChange={(e) => setFormData({ ...formData, preservation_method: e.target.value })}
                                className="w-full rounded-sm border border-neutral-300 bg-transparent px-3 py-1.5 text-xs text-neutral-900 dark:border-neutral-700 dark:text-neutral-100 dark:bg-neutral-900"
                            >
                                <option value="wet">wet (fluid ethanol/formalin)</option>
                                <option value="dry">dry (herbarium/skin/mount)</option>
                                <option value="frozen">frozen (cryo tissue -80°C)</option>
                                <option value="skeletal">skeletal (osteological prep)</option>
                                <option value="pinned">pinned (entomological)</option>
                                <option value="slide">slide (microscopic mount)</option>
                                <option value="tissue">tissue (DNA aliquot)</option>
                            </select>
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Storage Location *
                            </label>
                            <input
                                type="text"
                                required
                                value={formData.repository_location}
                                onChange={(e) => setFormData({ ...formData, repository_location: e.target.value })}
                                placeholder="Cabinet A-12, Shelf 03"
                                className="w-full rounded-sm border border-neutral-300 bg-transparent px-3 py-1.5 text-xs text-neutral-900 dark:border-neutral-700 dark:text-neutral-100"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Status *
                            </label>
                            <select
                                value={formData.status}
                                onChange={(e) => setFormData({ ...formData, status: e.target.value })}
                                className="w-full rounded-sm border border-neutral-300 bg-transparent px-3 py-1.5 text-xs text-neutral-900 dark:border-neutral-700 dark:text-neutral-100 dark:bg-neutral-900"
                            >
                                <option value="active">Active (In Repository)</option>
                                <option value="loaned">Loaned</option>
                                <option value="under_review">Under Review</option>
                                <option value="archived">Archived</option>
                                <option value="deaccessioned">Deaccessioned</option>
                            </select>
                        </div>
                    </div>

                    {/* Associated Collections & Locations */}
                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label className="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Collection Archive
                            </label>
                            <select
                                value={formData.collection_id}
                                onChange={(e) => setFormData({ ...formData, collection_id: e.target.value })}
                                className="w-full rounded-sm border border-neutral-300 bg-transparent px-3 py-1.5 text-xs text-neutral-900 dark:border-neutral-700 dark:text-neutral-100 dark:bg-neutral-900"
                            >
                                <option value="">-- None / Independent --</option>
                                {collections.map((c) => (
                                    <option key={c.id} value={c.id}>
                                        {c.name}
                                    </option>
                                ))}
                            </select>
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Sampling Location
                            </label>
                            <select
                                value={formData.location_id}
                                onChange={(e) => setFormData({ ...formData, location_id: e.target.value })}
                                className="w-full rounded-sm border border-neutral-300 bg-transparent px-3 py-1.5 text-xs text-neutral-900 dark:border-neutral-700 dark:text-neutral-100 dark:bg-neutral-900"
                            >
                                <option value="">-- None / Unrecorded --</option>
                                {locations.map((l) => (
                                    <option key={l.id} value={l.id}>
                                        {l.name} ({l.province})
                                    </option>
                                ))}
                            </select>
                        </div>
                    </div>

                    {/* Collector & Date */}
                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label className="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Primary Collector
                            </label>
                            <input
                                type="text"
                                value={formData.collector}
                                onChange={(e) => setFormData({ ...formData, collector: e.target.value })}
                                placeholder="Collector Name / Team"
                                className="w-full rounded-sm border border-neutral-300 bg-transparent px-3 py-1.5 text-xs text-neutral-900 dark:border-neutral-700 dark:text-neutral-100"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Collection Date
                            </label>
                            <input
                                type="date"
                                value={formData.collection_date}
                                onChange={(e) => setFormData({ ...formData, collection_date: e.target.value })}
                                className="w-full rounded-sm border border-neutral-300 bg-transparent px-3 py-1.5 text-xs text-neutral-900 dark:border-neutral-700 dark:text-neutral-100"
                            />
                        </div>
                    </div>

                    {/* Description */}
                    <div>
                        <label className="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                            Physical Description & Field Notes
                        </label>
                        <textarea
                            rows={3}
                            value={formData.description}
                            onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                            placeholder="Detailed voucher condition, anatomical components, or field observations..."
                            className="w-full rounded-sm border border-neutral-300 bg-transparent px-3 py-1.5 text-xs text-neutral-900 dark:border-neutral-700 dark:text-neutral-100"
                        />
                    </div>

                    {/* Modal Footer */}
                    <div className="flex items-center justify-end gap-3 pt-4 border-t border-neutral-200 dark:border-neutral-800">
                        <button
                            type="button"
                            onClick={onClose}
                            className="rounded-sm border border-neutral-300 px-4 py-2 text-xs font-medium text-neutral-700 transition-colors hover:bg-neutral-100 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            disabled={isSaving}
                            className="flex items-center gap-2 rounded-sm bg-black px-4 py-2 text-xs font-medium text-white transition-opacity hover:opacity-90 disabled:opacity-50 dark:bg-white dark:text-black"
                        >
                            <Save className="size-3.5" />
                            <span>{isSaving ? 'Saving...' : isEdit ? 'Save Changes' : 'Register Specimen'}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
};
