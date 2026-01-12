import { apiGet, apiPost } from "@/utils/api";

// Dummy data generators
const generateDummyNeracaLajur = (startDate, endDate) => {
    const accounts = [
        // Asset accounts
        {
            account_code: "1-1000",
            account_name: "Kas",
            account_type: "asset",
            balance: 50000000,
            neraca_debit: 50000000,
            neraca_credit: 0,
            laba_rugi_debit: 0,
            laba_rugi_credit: 0,
        },
        {
            account_code: "1-1100",
            account_name: "Bank BCA",
            account_type: "asset",
            balance: 75000000,
            neraca_debit: 75000000,
            neraca_credit: 0,
            laba_rugi_debit: 0,
            laba_rugi_credit: 0,
        },
        {
            account_code: "1-1200",
            account_name: "Piutang Usaha",
            account_type: "asset",
            balance: 25000000,
            neraca_debit: 25000000,
            neraca_credit: 0,
            laba_rugi_debit: 0,
            laba_rugi_credit: 0,
        },
        {
            account_code: "1-1300",
            account_name: "Persediaan Barang",
            account_type: "asset",
            balance: 30000000,
            neraca_debit: 30000000,
            neraca_credit: 0,
            laba_rugi_debit: 0,
            laba_rugi_credit: 0,
        },
        {
            account_code: "1-1500",
            account_name: "Peralatan",
            account_type: "asset",
            balance: 15000000,
            neraca_debit: 15000000,
            neraca_credit: 0,
            laba_rugi_debit: 0,
            laba_rugi_credit: 0,
        },

        // Liability accounts
        {
            account_code: "2-1000",
            account_name: "Utang Usaha",
            account_type: "liability",
            balance: 20000000,
            neraca_debit: 0,
            neraca_credit: 20000000,
            laba_rugi_debit: 0,
            laba_rugi_credit: 0,
        },
        {
            account_code: "2-1100",
            account_name: "Utang Pajak",
            account_type: "liability",
            balance: 5000000,
            neraca_debit: 0,
            neraca_credit: 5000000,
            laba_rugi_debit: 0,
            laba_rugi_credit: 0,
        },

        // Equity accounts
        {
            account_code: "3-1000",
            account_name: "Modal Pemilik",
            account_type: "equity",
            balance: 100000000,
            neraca_debit: 0,
            neraca_credit: 100000000,
            laba_rugi_debit: 0,
            laba_rugi_credit: 0,
        },

        // Revenue accounts
        {
            account_code: "4-1000",
            account_name: "Penjualan Barang",
            account_type: "revenue",
            balance: 150000000,
            neraca_debit: 0,
            neraca_credit: 0,
            laba_rugi_debit: 0,
            laba_rugi_credit: 150000000,
        },
        {
            account_code: "4-1100",
            account_name: "Pendapatan Jasa",
            account_type: "revenue",
            balance: 25000000,
            neraca_debit: 0,
            neraca_credit: 0,
            laba_rugi_debit: 0,
            laba_rugi_credit: 25000000,
        },

        // Expense accounts
        {
            account_code: "5-1000",
            account_name: "Harga Pokok Penjualan",
            account_type: "expense",
            balance: 80000000,
            neraca_debit: 0,
            neraca_credit: 0,
            laba_rugi_debit: 80000000,
            laba_rugi_credit: 0,
        },
        {
            account_code: "5-1100",
            account_name: "Beban Gaji",
            account_type: "expense",
            balance: 30000000,
            neraca_debit: 0,
            neraca_credit: 0,
            laba_rugi_debit: 30000000,
            laba_rugi_credit: 0,
        },
        {
            account_code: "5-1200",
            account_name: "Beban Sewa",
            account_type: "expense",
            balance: 10000000,
            neraca_debit: 0,
            neraca_credit: 0,
            laba_rugi_debit: 10000000,
            laba_rugi_credit: 0,
        },
        {
            account_code: "5-1300",
            account_name: "Beban Listrik",
            account_type: "expense",
            balance: 5000000,
            neraca_debit: 0,
            neraca_credit: 0,
            laba_rugi_debit: 5000000,
            laba_rugi_credit: 0,
        },
    ];

    return {
        data: accounts,
        period: {
            start_date: startDate,
            end_date: endDate,
        },
    };
};

const generateDummyNeraca = (endDate) => {
    return {
        assets: [
            { code: "1-1000", name: "Kas", balance: 50000000, level: 1 },
            { code: "1-1100", name: "Bank BCA", balance: 75000000, level: 1 },
            {
                code: "1-1200",
                name: "Piutang Usaha",
                balance: 25000000,
                level: 1,
            },
            {
                code: "1-1300",
                name: "Persediaan Barang",
                balance: 30000000,
                level: 1,
            },
            { code: "1-1400", name: "Peralatan", balance: 15000000, level: 2 },
            { code: "1-1500", name: "Kendaraan", balance: 50000000, level: 2 },
            { code: "1-1600", name: "Gedung", balance: 100000000, level: 2 },
        ],
        liabilities: [
            {
                code: "2-1000",
                name: "Utang Usaha",
                balance: 20000000,
                level: 1,
            },
            { code: "2-1100", name: "Utang Pajak", balance: 5000000, level: 1 },
            { code: "2-1200", name: "Utang Bank", balance: 10000000, level: 1 },
        ],
        equity: [
            {
                code: "3-1000",
                name: "Modal Pemilik",
                balance: 100000000,
                level: 1,
            },
            {
                code: "3-1100",
                name: "Laba Ditahan",
                balance: 25000000,
                level: 1,
            },
        ],
        totals: {
            assets: 345000000,
            liabilities: 35000000,
            equity: 125000000,
            liabilities_equity: 160000000,
        },
        period: {
            end_date: endDate,
        },
    };
};




const reportService = {
    // Neraca Lajur (Worksheet)
    async getNeracaLajur(startDate, endDate) {
        try {
            const response = await apiGet("reports/neraca-lajur", {
                params: {
                    start_date: startDate,
                    end_date: endDate,
                }
            });
            return response;
        } catch (error) {
            console.error("Error fetching neraca lajur:", error);
            throw error;
        }
    },

    // Neraca (Balance Sheet)
    async getNeraca(endDate) {
        try {
            const response = await apiGet("reports/neraca", {
                params: {
                    end_date: endDate,
                }
            });
            return response;
        } catch (error) {
            console.error("Error fetching neraca:", error);
            throw error;
        }
    },

    // Laba Rugi (Income Statement)
    async getLabaRugi(startDate, endDate) {
        try {
            const response = await apiGet("reports/laba-rugi", {
                params: {
                    start_date: startDate,
                    end_date: endDate,
                }
            });
            return response;
        } catch (error) {
            console.error("Error fetching laba rugi:", error);
            throw error;
        }
    },

    // Perubahan Modal (Statement of Changes in Equity)
    async getPerubahanModal(startDate, endDate) {
        try {
            const response = await apiGet("reports/perubahan-modal", {
                params: {
                    start_date: startDate,
                    end_date: endDate,
                }
            });
            return response;
        } catch (error) {
            console.error("Error fetching perubahan modal:", error);
            throw error;
        }
    },

    // Arus Kas (Cash Flow Statement)
    async getArusKas(startDate, endDate) {
        try {
            const response = await apiGet("reports/arus-kas", {
                params: {
                    start_date: startDate,
                    end_date: endDate,
                }
            });
            return response;
        } catch (error) {
            console.error("Error fetching arus kas:", error);
            throw error;
        }
    },

    // Export functions
    async exportNeracaLajur(startDate, endDate, format = "pdf") {
        try {
            // For export, we need to handle file download differently
            const token = localStorage.getItem('token');
            const apiUrl = `${import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000'}/api/reports/neraca-lajur/export`;

            const params = new URLSearchParams({
                start_date: startDate,
                end_date: endDate,
                format: format,
            });

            const response = await fetch(`${apiUrl}?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': format === 'pdf' ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                },
            });

            if (!response.ok) {
                throw new Error('Export failed');
            }

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `neraca_lajur_${startDate}_${endDate}.${format}`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);

            return { success: true };
        } catch (error) {
            console.error("Error exporting neraca lajur:", error);
            throw error;
        }
    },

    async exportNeraca(endDate, format = "pdf") {
        try {
            // For export, we need to handle file download differently
            const token = localStorage.getItem('token');
            const apiUrl = `${import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000'}/api/reports/neraca/export`;

            const params = new URLSearchParams({
                end_date: endDate,
                format: format,
            });

            const response = await fetch(`${apiUrl}?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': format === 'pdf' ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                },
            });

            if (!response.ok) {
                throw new Error('Export failed');
            }

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `neraca_${endDate}.${format}`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);

            return { success: true };
        } catch (error) {
            console.error("Error exporting neraca:", error);
            throw error;
        }
    },

    async exportLabaRugi(startDate, endDate, format = "pdf") {
        try {
            // For export, we need to handle file download differently
            const token = localStorage.getItem('token');
            const apiUrl = `${import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000'}/api/reports/laba-rugi/export`;

            const params = new URLSearchParams({
                start_date: startDate,
                end_date: endDate,
                format: format,
            });

            const response = await fetch(`${apiUrl}?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': format === 'pdf' ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                },
            });

            if (!response.ok) {
                throw new Error('Export failed');
            }

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `laba_rugi_${startDate}_${endDate}.${format}`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);

            return { success: true };
        } catch (error) {
            console.error("Error exporting laba rugi:", error);
            throw error;
        }
    },

    async exportPerubahanModal(startDate, endDate, format = "pdf") {
        try {
            // For export, we need to handle file download differently
            const token = localStorage.getItem('token');
            const apiUrl = `${import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000'}/api/reports/perubahan-modal/export`;

            const params = new URLSearchParams({
                start_date: startDate,
                end_date: endDate,
                format: format,
            });

            const response = await fetch(`${apiUrl}?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': format === 'pdf' ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                },
            });

            if (!response.ok) {
                throw new Error('Export failed');
            }

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `perubahan_modal_${startDate}_${endDate}.${format}`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);

            return { success: true };
        } catch (error) {
            console.error("Error exporting perubahan modal:", error);
            throw error;
        }
    },

    async exportArusKas(startDate, endDate, format = "pdf") {
        try {
            // For export, we need to handle file download differently
            const token = localStorage.getItem('token');
            const apiUrl = `${import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000'}/api/reports/arus-kas/export`;

            const params = new URLSearchParams({
                start_date: startDate,
                end_date: endDate,
                format: format,
            });

            const response = await fetch(`${apiUrl}?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': format === 'pdf' ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                },
            });

            if (!response.ok) {
                throw new Error('Export failed');
            }

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `arus_kas_${startDate}_${endDate}.${format}`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);

            return { success: true };
        } catch (error) {
            console.error("Error exporting arus kas:", error);
            throw error;
        }
    },
};

export default reportService;
