import React, { useState } from 'react';
import { X, Save, AlertCircle } from 'lucide-react';
import { adminApi } from '@/lib/admin-api';

// --- 1. TAXON MODAL ---
interface TaxonModalProps {
    isOpen: boolean;
    onClose: () => void;
    onCreated: () => void;
}

export const TaxonModal: React.FC<TaxonModalProps> = ({ isOpen, onClose, onCreated }) => {
    const [data, setData] = useState({
        scientific_name: '',
        common_name: '',
        kingdom: 'Animalia',
        phylum: '',
        class: '',
        order: '',
        family: '',
        genus: '',
        species: '',
        authority: '',
        description: '',
    });
    const [isSaving, setIsSaving] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setIsSaving(true);
        setError(null);
        try {
            const res = await adminApi.createTaxon(data);
            if (res.success) {
                onCreated();
                onClose();
            } else {
                setError(res.message || 'Failed to create taxon.');
            }
        } catch {
            setError('Server error.');
        } finally {
            setIsSaving(false);
        }
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs">
            <div className="flex max-h-[90vh] w-full max-w-lg flex-col rounded-md border border-neutral-300 bg-white shadow-xl dark:border-neutral-800 dark:bg-neutral-900">
                <div className="flex items-center justify-between border-b border-neutral-200 px-6 py-4 dark:border-neutral-800">
                    <h3 className="text-sm font-semibold text-neutral-950 dark:text-neutral-50">Add Taxonomic Classification</h3>
                    <button onClick={onClose} className="p-1 text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100">
                        <X className="size-4" />
                    </button>
                </div>
                <form onSubmit={handleSubmit} className="flex-1 overflow-y-auto p-6 space-y-3 text-xs">
                    {error && (
                        <div className="flex items-center gap-2 rounded-sm border border-neutral-400 p-2 text-neutral-900 dark:text-neutral-200">
                            <AlertCircle className="size-4 shrink-0" />
                            <span>{error}</span>
                        </div>
                    )}
                    <div>
                        <label className="block font-medium mb-1">Scientific Name *</label>
                        <input
                            type="text"
                            required
                            placeholder="e.g. Acerodon jubatus"
                            value={data.scientific_name}
                            onChange={(e) => setData({ ...data, scientific_name: e.target.value })}
                            className="w-full rounded-sm border border-neutral-300 bg-transparent px-2.5 py-1.5 font-italic dark:border-neutral-700"
                        />
                    </div>
                    <div className="grid grid-cols-2 gap-3">
                        <div>
                            <label className="block font-medium mb-1">Common Name</label>
                            <input
                                type="text"
                                placeholder="e.g. Golden-crowned Flying Fox"
                                value={data.common_name}
                                onChange={(e) => setData({ ...data, common_name: e.target.value })}
                                className="w-full rounded-sm border border-neutral-300 bg-transparent px-2.5 py-1.5 dark:border-neutral-700"
                            />
                        </div>
                        <div>
                            <label className="block font-medium mb-1">Kingdom *</label>
                            <select
                                value={data.kingdom}
                                onChange={(e) => setData({ ...data, kingdom: e.target.value })}
                                className="w-full rounded-sm border border-neutral-300 bg-transparent px-2.5 py-1.5 dark:border-neutral-700 dark:bg-neutral-900"
                            >
                                <option value="Animalia">Animalia</option>
                                <option value="Plantae">Plantae</option>
                                <option value="Fungi">Fungi</option>
                            </select>
                        </div>
                    </div>
                    <div className="grid grid-cols-2 gap-3">
                        <div>
                            <label className="block font-medium mb-1">Family</label>
                            <input
                                type="text"
                                placeholder="e.g. Pteropodidae"
                                value={data.family}
                                onChange={(e) => setData({ ...data, family: e.target.value })}
                                className="w-full rounded-sm border border-neutral-300 bg-transparent px-2.5 py-1.5 dark:border-neutral-700"
                            />
                        </div>
                        <div>
                            <label className="block font-medium mb-1">Authority</label>
                            <input
                                type="text"
                                placeholder="e.g. Eschscholtz, 1831"
                                value={data.authority}
                                onChange={(e) => setData({ ...data, authority: e.target.value })}
                                className="w-full rounded-sm border border-neutral-300 bg-transparent px-2.5 py-1.5 dark:border-neutral-700"
                            />
                        </div>
                    </div>
                    <div className="grid grid-cols-2 gap-3">
                        <div>
                            <label className="block font-medium mb-1">Genus</label>
                            <input
                                type="text"
                                placeholder="e.g. Acerodon"
                                value={data.genus}
                                onChange={(e) => setData({ ...data, genus: e.target.value })}
                                className="w-full rounded-sm border border-neutral-300 bg-transparent px-2.5 py-1.5 italic dark:border-neutral-700"
                            />
                        </div>
                        <div>
                            <label className="block font-medium mb-1">Species</label>
                            <input
                                type="text"
                                placeholder="e.g. jubatus"
                                value={data.species}
                                onChange={(e) => setData({ ...data, species: e.target.value })}
                                className="w-full rounded-sm border border-neutral-300 bg-transparent px-2.5 py-1.5 italic dark:border-neutral-700"
                            />
                        </div>
                    </div>
                    <div>
                        <label className="block font-medium mb-1">Taxonomic Description</label>
                        <textarea
                            rows={2}
                            value={data.description}
                            onChange={(e) => setData({ ...data, description: e.target.value })}
                            className="w-full rounded-sm border border-neutral-300 bg-transparent px-2.5 py-1.5 dark:border-neutral-700"
                        />
                    </div>
                    <div className="flex justify-end gap-2 pt-3 border-t border-neutral-200 dark:border-neutral-800">
                        <button type="button" onClick={onClose} className="rounded-sm border border-neutral-300 px-3 py-1.5 text-neutral-700 dark:border-neutral-700 dark:text-neutral-300">
                            Cancel
                        </button>
                        <button type="submit" disabled={isSaving} className="rounded-sm bg-black px-3 py-1.5 text-white dark:bg-white dark:text-black">
                            {isSaving ? 'Saving...' : 'Add Taxon'}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
};

// --- 2. COLLECTION MODAL ---
interface CollectionModalProps {
    isOpen: boolean;
    onClose: () => void;
    onCreated: () => void;
}

export const CollectionModal: React.FC<CollectionModalProps> = ({ isOpen, onClose, onCreated }) => {
    const [data, setData] = useState({
        name: '',
        description: '',
        collection_type: 'General Natural History',
        collector: 'Institutional Curatorial Unit',
        date_started: new Date().toISOString().split('T')[0],
        status: 'active',
    });
    const [isSaving, setIsSaving] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setIsSaving(true);
        setError(null);
        try {
            const res = await adminApi.createCollection(data);
            if (res.success) {
                onCreated();
                onClose();
            } else {
                setError(res.message || 'Failed to create collection.');
            }
        } catch {
            setError('Server error.');
        } finally {
            setIsSaving(false);
        }
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs">
            <div className="flex max-h-[90vh] w-full max-w-md flex-col rounded-md border border-neutral-300 bg-white shadow-xl dark:border-neutral-800 dark:bg-neutral-900">
                <div className="flex items-center justify-between border-b border-neutral-200 px-6 py-4 dark:border-neutral-800">
                    <h3 className="text-sm font-semibold text-neutral-950 dark:text-neutral-50">Create Collection Archive</h3>
                    <button onClick={onClose} className="p-1 text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100">
                        <X className="size-4" />
                    </button>
                </div>
                <form onSubmit={handleSubmit} className="flex-1 overflow-y-auto p-6 space-y-3 text-xs">
                    {error && (
                        <div className="flex items-center gap-2 rounded-sm border border-neutral-400 p-2 text-neutral-900 dark:text-neutral-200">
                            <AlertCircle className="size-4 shrink-0" />
                            <span>{error}</span>
                        </div>
                    )}
                    <div>
                        <label className="block font-medium mb-1">Collection Name *</label>
                        <input
                            type="text"
                            required
                            placeholder="e.g. Visayan Karst Fauna Repository"
                            value={data.name}
                            onChange={(e) => setData({ ...data, name: e.target.value })}
                            className="w-full rounded-sm border border-neutral-300 bg-transparent px-2.5 py-1.5 dark:border-neutral-700"
                        />
                    </div>
                    <div>
                        <label className="block font-medium mb-1">Discipline / Type</label>
                        <input
                            type="text"
                            placeholder="e.g. Herpetology & Cave Biology"
                            value={data.collection_type}
                            onChange={(e) => setData({ ...data, collection_type: e.target.value })}
                            className="w-full rounded-sm border border-neutral-300 bg-transparent px-2.5 py-1.5 dark:border-neutral-700"
                        />
                    </div>
                    <div className="grid grid-cols-2 gap-3">
                        <div>
                            <label className="block font-medium mb-1">Collector / Unit</label>
                            <input
                                type="text"
                                value={data.collector}
                                onChange={(e) => setData({ ...data, collector: e.target.value })}
                                className="w-full rounded-sm border border-neutral-300 bg-transparent px-2.5 py-1.5 dark:border-neutral-700"
                            />
                        </div>
                        <div>
                            <label className="block font-medium mb-1">Date Established</label>
                            <input
                                type="date"
                                value={data.date_started}
                                onChange={(e) => setData({ ...data, date_started: e.target.value })}
                                className="w-full rounded-sm border border-neutral-300 bg-transparent px-2.5 py-1.5 dark:border-neutral-700"
                            />
                        </div>
                    </div>
                    <div>
                        <label className="block font-medium mb-1">Description</label>
                        <textarea
                            rows={3}
                            placeholder="Scope and historical background of the natural history archive..."
                            value={data.description}
                            onChange={(e) => setData({ ...data, description: e.target.value })}
                            className="w-full rounded-sm border border-neutral-300 bg-transparent px-2.5 py-1.5 dark:border-neutral-700"
                        />
                    </div>
                    <div className="flex justify-end gap-2 pt-3 border-t border-neutral-200 dark:border-neutral-800">
                        <button type="button" onClick={onClose} className="rounded-sm border border-neutral-300 px-3 py-1.5 text-neutral-700 dark:border-neutral-700 dark:text-neutral-300">
                            Cancel
                        </button>
                        <button type="submit" disabled={isSaving} className="rounded-sm bg-black px-3 py-1.5 text-white dark:bg-white dark:text-black">
                            {isSaving ? 'Saving...' : 'Create Archive'}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
};

// --- 3. LOCATION MODAL ---
interface LocationModalProps {
    isOpen: boolean;
    onClose: () => void;
    onCreated: () => void;
}

export const LocationModal: React.FC<LocationModalProps> = ({ isOpen, onClose, onCreated }) => {
    const [data, setData] = useState({
        name: '',
        province: '',
        municipality: '',
        barangay: '',
        latitude: 14.0,
        longitude: 121.0,
        habitat: '',
        description: '',
    });
    const [isSaving, setIsSaving] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setIsSaving(true);
        setError(null);
        try {
            const res = await adminApi.createLocation(data);
            if (res.success) {
                onCreated();
                onClose();
            } else {
                setError(res.message || 'Failed to create location.');
            }
        } catch {
            setError('Server error.');
        } finally {
            setIsSaving(false);
        }
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs">
            <div className="flex max-h-[90vh] w-full max-w-md flex-col rounded-md border border-neutral-300 bg-white shadow-xl dark:border-neutral-800 dark:bg-neutral-900">
                <div className="flex items-center justify-between border-b border-neutral-200 px-6 py-4 dark:border-neutral-800">
                    <h3 className="text-sm font-semibold text-neutral-950 dark:text-neutral-50">Register Sampling Locality</h3>
                    <button onClick={onClose} className="p-1 text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100">
                        <X className="size-4" />
                    </button>
                </div>
                <form onSubmit={handleSubmit} className="flex-1 overflow-y-auto p-6 space-y-3 text-xs">
                    {error && (
                        <div className="flex items-center gap-2 rounded-sm border border-neutral-400 p-2 text-neutral-900 dark:text-neutral-200">
                            <AlertCircle className="size-4 shrink-0" />
                            <span>{error}</span>
                        </div>
                    )}
                    <div>
                        <label className="block font-medium mb-1">Locality Name *</label>
                        <input
                            type="text"
                            required
                            placeholder="e.g. Mount Victoria Ridge"
                            value={data.name}
                            onChange={(e) => setData({ ...data, name: e.target.value })}
                            className="w-full rounded-sm border border-neutral-300 bg-transparent px-2.5 py-1.5 dark:border-neutral-700"
                        />
                    </div>
                    <div className="grid grid-cols-2 gap-3">
                        <div>
                            <label className="block font-medium mb-1">Province</label>
                            <input
                                type="text"
                                placeholder="e.g. Palawan"
                                value={data.province}
                                onChange={(e) => setData({ ...data, province: e.target.value })}
                                className="w-full rounded-sm border border-neutral-300 bg-transparent px-2.5 py-1.5 dark:border-neutral-700"
                            />
                        </div>
                        <div>
                            <label className="block font-medium mb-1">Municipality</label>
                            <input
                                type="text"
                                placeholder="e.g. Narra"
                                value={data.municipality}
                                onChange={(e) => setData({ ...data, municipality: e.target.value })}
                                className="w-full rounded-sm border border-neutral-300 bg-transparent px-2.5 py-1.5 dark:border-neutral-700"
                            />
                        </div>
                    </div>
                    <div className="grid grid-cols-2 gap-3">
                        <div>
                            <label className="block font-medium mb-1">Latitude (°N) *</label>
                            <input
                                type="number"
                                step="any"
                                required
                                value={data.latitude}
                                onChange={(e) => setData({ ...data, latitude: parseFloat(e.target.value) || 0 })}
                                className="w-full rounded-sm border border-neutral-300 bg-transparent px-2.5 py-1.5 font-mono dark:border-neutral-700"
                            />
                        </div>
                        <div>
                            <label className="block font-medium mb-1">Longitude (°E) *</label>
                            <input
                                type="number"
                                step="any"
                                required
                                value={data.longitude}
                                onChange={(e) => setData({ ...data, longitude: parseFloat(e.target.value) || 0 })}
                                className="w-full rounded-sm border border-neutral-300 bg-transparent px-2.5 py-1.5 font-mono dark:border-neutral-700"
                            />
                        </div>
                    </div>
                    <div>
                        <label className="block font-medium mb-1">Habitat Characterization</label>
                        <input
                            type="text"
                            placeholder="e.g. Ultramafic serpentine montane cloud forest"
                            value={data.habitat}
                            onChange={(e) => setData({ ...data, habitat: e.target.value })}
                            className="w-full rounded-sm border border-neutral-300 bg-transparent px-2.5 py-1.5 dark:border-neutral-700"
                        />
                    </div>
                    <div className="flex justify-end gap-2 pt-3 border-t border-neutral-200 dark:border-neutral-800">
                        <button type="button" onClick={onClose} className="rounded-sm border border-neutral-300 px-3 py-1.5 text-neutral-700 dark:border-neutral-700 dark:text-neutral-300">
                            Cancel
                        </button>
                        <button type="submit" disabled={isSaving} className="rounded-sm bg-black px-3 py-1.5 text-white dark:bg-white dark:text-black">
                            {isSaving ? 'Saving...' : 'Register Locality'}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
};

// --- 4. RESEARCHER MODAL ---
interface ResearcherModalProps {
    isOpen: boolean;
    onClose: () => void;
    onCreated: () => void;
}

export const ResearcherModal: React.FC<ResearcherModalProps> = ({ isOpen, onClose, onCreated }) => {
    const [data, setData] = useState({
        name: '',
        email: '',
        institution: '',
        department: '',
        specialization: '',
        contact_information: '',
    });
    const [isSaving, setIsSaving] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setIsSaving(true);
        setError(null);
        try {
            const res = await adminApi.createResearcher(data);
            if (res.success) {
                onCreated();
                onClose();
            } else {
                setError(res.message || 'Failed to create researcher.');
            }
        } catch {
            setError('Server error.');
        } finally {
            setIsSaving(false);
        }
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs">
            <div className="flex max-h-[90vh] w-full max-w-md flex-col rounded-md border border-neutral-300 bg-white shadow-xl dark:border-neutral-800 dark:bg-neutral-900">
                <div className="flex items-center justify-between border-b border-neutral-200 px-6 py-4 dark:border-neutral-800">
                    <h3 className="text-sm font-semibold text-neutral-950 dark:text-neutral-50">Register Scientific Researcher</h3>
                    <button onClick={onClose} className="p-1 text-neutral-400 hover:text-neutral-900 dark:hover:text-neutral-100">
                        <X className="size-4" />
                    </button>
                </div>
                <form onSubmit={handleSubmit} className="flex-1 overflow-y-auto p-6 space-y-3 text-xs">
                    {error && (
                        <div className="flex items-center gap-2 rounded-sm border border-neutral-400 p-2 text-neutral-900 dark:text-neutral-200">
                            <AlertCircle className="size-4 shrink-0" />
                            <span>{error}</span>
                        </div>
                    )}
                    <div>
                        <label className="block font-medium mb-1">Full Name & Title *</label>
                        <input
                            type="text"
                            required
                            placeholder="e.g. Dr. Maria Clara Santos"
                            value={data.name}
                            onChange={(e) => setData({ ...data, name: e.target.value })}
                            className="w-full rounded-sm border border-neutral-300 bg-transparent px-2.5 py-1.5 dark:border-neutral-700"
                        />
                    </div>
                    <div>
                        <label className="block font-medium mb-1">Institutional Email *</label>
                        <input
                            type="email"
                            required
                            placeholder="e.g. m.santos@up.edu.ph"
                            value={data.email}
                            onChange={(e) => setData({ ...data, email: e.target.value })}
                            className="w-full rounded-sm border border-neutral-300 bg-transparent px-2.5 py-1.5 dark:border-neutral-700"
                        />
                    </div>
                    <div className="grid grid-cols-2 gap-3">
                        <div>
                            <label className="block font-medium mb-1">Institution</label>
                            <input
                                type="text"
                                placeholder="e.g. University of the Philippines"
                                value={data.institution}
                                onChange={(e) => setData({ ...data, institution: e.target.value })}
                                className="w-full rounded-sm border border-neutral-300 bg-transparent px-2.5 py-1.5 dark:border-neutral-700"
                            />
                        </div>
                        <div>
                            <label className="block font-medium mb-1">Department</label>
                            <input
                                type="text"
                                placeholder="e.g. Institute of Biology"
                                value={data.department}
                                onChange={(e) => setData({ ...data, department: e.target.value })}
                                className="w-full rounded-sm border border-neutral-300 bg-transparent px-2.5 py-1.5 dark:border-neutral-700"
                            />
                        </div>
                    </div>
                    <div>
                        <label className="block font-medium mb-1">Research Specialization</label>
                        <input
                            type="text"
                            placeholder="e.g. Avian Genetics & Accipitrid Phylogeography"
                            value={data.specialization}
                            onChange={(e) => setData({ ...data, specialization: e.target.value })}
                            className="w-full rounded-sm border border-neutral-300 bg-transparent px-2.5 py-1.5 dark:border-neutral-700"
                        />
                    </div>
                    <div className="flex justify-end gap-2 pt-3 border-t border-neutral-200 dark:border-neutral-800">
                        <button type="button" onClick={onClose} className="rounded-sm border border-neutral-300 px-3 py-1.5 text-neutral-700 dark:border-neutral-700 dark:text-neutral-300">
                            Cancel
                        </button>
                        <button type="submit" disabled={isSaving} className="rounded-sm bg-black px-3 py-1.5 text-white dark:bg-white dark:text-black">
                            {isSaving ? 'Saving...' : 'Register Researcher'}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
};
