import React, { useState, useEffect } from 'react';
import { X, Send, AlertCircle } from 'lucide-react';
import { adminApi } from '@/lib/admin-api';

interface LoanModalProps {
    isOpen: boolean;
    onClose: () => void;
    onCreated: () => void;
    preSelectedSpecimen?: any | null;
    activeSpecimens: any[];
    researchers: any[];
}

export const LoanModal: React.FC<LoanModalProps> = ({
    isOpen,
    onClose,
    onCreated,
    preSelectedSpecimen,
    activeSpecimens,
    researchers,
}) => {
    const [specimenId, setSpecimenId] = useState('');
    const [researcherId, setResearcherId] = useState('');
    const [borrowerName, setBorrowerName] = useState('');
    const [purpose, setPurpose] = useState('');
    const [loanDate, setLoanDate] = useState(new Date().toISOString().split('T')[0]);
    const [expectedReturnDate, setExpectedReturnDate] = useState('');
    const [notes, setNotes] = useState('');

    const [isSaving, setIsSaving] = useState(false);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);

    useEffect(() => {
        if (preSelectedSpecimen) {
            setSpecimenId(preSelectedSpecimen.id);
        } else if (activeSpecimens.length > 0 && !specimenId) {
            setSpecimenId(activeSpecimens[0].id);
        }

        // Default return date: 3 months from now
        const returnDate = new Date();
        returnDate.setMonth(returnDate.getMonth() + 3);
        setExpectedReturnDate(returnDate.toISOString().split('T')[0]);

        if (researchers.length > 0 && !researcherId) {
            setResearcherId(researchers[0].id);
            setBorrowerName(researchers[0].name);
        }

        setErrorMessage(null);
    }, [isOpen, preSelectedSpecimen]);

    const handleResearcherChange = (id: string) => {
        setResearcherId(id);
        const res = researchers.find((r) => r.id === id);
        if (res) {
            setBorrowerName(res.name);
        }
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setIsSaving(true);
        setErrorMessage(null);

        try {
            const res = await adminApi.createLoan({
                specimen_id: specimenId,
                researcher_id: researcherId || undefined,
                borrower_name: borrowerName,
                purpose,
                loan_date: loanDate,
                expected_return_date: expectedReturnDate,
                notes,
            });

            if (res.success) {
                onCreated();
                onClose();
            } else {
                setErrorMessage(res.message || 'Loan creation failed.');
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
            <div className="flex max-h-[90vh] w-full max-w-lg flex-col rounded-md border border-neutral-300 bg-white shadow-xl dark:border-neutral-800 dark:bg-neutral-900">
                {/* Header */}
                <div className="flex items-center justify-between border-b border-neutral-200 px-6 py-4 dark:border-neutral-800">
                    <div>
                        <h3 className="text-base font-semibold text-neutral-950 dark:text-neutral-50">
                            Create Specimen Loan Transaction
                        </h3>
                        <p className="text-xs text-neutral-500 dark:text-neutral-400">
                            Issue voucher for research, tomography, or taxonomic examination.
                        </p>
                    </div>
                    <button
                        onClick={onClose}
                        className="rounded-sm p-1 text-neutral-500 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:bg-neutral-800"
                    >
                        <X className="size-5" />
                    </button>
                </div>

                {/* Form Body */}
                <form onSubmit={handleSubmit} className="flex-1 overflow-y-auto p-6 space-y-4">
                    {errorMessage && (
                        <div className="flex items-start gap-2 rounded-sm border border-neutral-400 bg-neutral-100 p-3 text-xs text-neutral-900 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200">
                            <AlertCircle className="size-4 shrink-0 mt-0.5 text-neutral-700 dark:text-neutral-300" />
                            <span>{errorMessage}</span>
                        </div>
                    )}

                    {/* Specimen Picker */}
                    <div>
                        <label className="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                            Specimen to Loan *
                        </label>
                        <select
                            required
                            value={specimenId}
                            onChange={(e) => setSpecimenId(e.target.value)}
                            className="w-full rounded-sm border border-neutral-300 bg-transparent px-3 py-1.5 text-xs text-neutral-900 dark:border-neutral-700 dark:text-neutral-100 dark:bg-neutral-900"
                        >
                            <option value="">-- Select Active Specimen --</option>
                            {activeSpecimens.map((s) => (
                                <option key={s.id} value={s.id}>
                                    {s.specimen_code} — {s.scientific_name} ({s.preservation_method})
                                </option>
                            ))}
                        </select>
                    </div>

                    {/* Researcher / Borrower */}
                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label className="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Registered Investigator
                            </label>
                            <select
                                value={researcherId}
                                onChange={(e) => handleResearcherChange(e.target.value)}
                                className="w-full rounded-sm border border-neutral-300 bg-transparent px-3 py-1.5 text-xs text-neutral-900 dark:border-neutral-700 dark:text-neutral-100 dark:bg-neutral-900"
                            >
                                <option value="">-- Independent / External --</option>
                                {researchers.map((r) => (
                                    <option key={r.id} value={r.id}>
                                        {r.name} ({r.institution})
                                    </option>
                                ))}
                            </select>
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Borrower Name *
                            </label>
                            <input
                                type="text"
                                required
                                value={borrowerName}
                                onChange={(e) => setBorrowerName(e.target.value)}
                                placeholder="Investigator or Curator"
                                className="w-full rounded-sm border border-neutral-300 bg-transparent px-3 py-1.5 text-xs text-neutral-900 dark:border-neutral-700 dark:text-neutral-100"
                            />
                        </div>
                    </div>

                    {/* Loan Date & Expected Return Date */}
                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label className="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Loan Issue Date *
                            </label>
                            <input
                                type="date"
                                required
                                value={loanDate}
                                onChange={(e) => setLoanDate(e.target.value)}
                                className="w-full rounded-sm border border-neutral-300 bg-transparent px-3 py-1.5 text-xs text-neutral-900 dark:border-neutral-700 dark:text-neutral-100"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                                Expected Return Date *
                            </label>
                            <input
                                type="date"
                                required
                                value={expectedReturnDate}
                                onChange={(e) => setExpectedReturnDate(e.target.value)}
                                className="w-full rounded-sm border border-neutral-300 bg-transparent px-3 py-1.5 text-xs text-neutral-900 dark:border-neutral-700 dark:text-neutral-100"
                            />
                        </div>
                    </div>

                    {/* Scientific Purpose */}
                    <div>
                        <label className="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                            Research Purpose & Method *
                        </label>
                        <textarea
                            required
                            rows={3}
                            value={purpose}
                            onChange={(e) => setPurpose(e.target.value)}
                            placeholder="e.g. Non-destructive micro-computed tomography scan of cranial osteology..."
                            className="w-full rounded-sm border border-neutral-300 bg-transparent px-3 py-1.5 text-xs text-neutral-900 dark:border-neutral-700 dark:text-neutral-100"
                        />
                    </div>

                    {/* Transport & Handling Notes */}
                    <div>
                        <label className="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1">
                            Transport & Special Conditions
                        </label>
                        <input
                            type="text"
                            value={notes}
                            onChange={(e) => setNotes(e.target.value)}
                            placeholder="e.g. Courier transport in dry ice; restricted from invasive sampling"
                            className="w-full rounded-sm border border-neutral-300 bg-transparent px-3 py-1.5 text-xs text-neutral-900 dark:border-neutral-700 dark:text-neutral-100"
                        />
                    </div>

                    {/* Footer Actions */}
                    <div className="flex items-center justify-end gap-3 pt-4 border-t border-neutral-200 dark:border-neutral-800">
                        <button
                            type="button"
                            onClick={onClose}
                            className="rounded-sm border border-neutral-300 px-4 py-2 text-xs font-medium text-neutral-700 hover:bg-neutral-100 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            disabled={isSaving}
                            className="flex items-center gap-2 rounded-sm bg-black px-4 py-2 text-xs font-medium text-white hover:opacity-90 disabled:opacity-50 dark:bg-white dark:text-black"
                        >
                            <Send className="size-3.5" />
                            <span>{isSaving ? 'Submitting...' : 'Submit Loan'}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
};
