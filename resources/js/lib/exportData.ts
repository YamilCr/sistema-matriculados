export interface ExportColumn<T> {
    key: string;
    label: string;
    value: (row: T) => string | number | null | undefined;
}

const escapeHtml = (value: string) =>
    value.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

function download(content: string, filename: string, mime: string) {
    const blob = new Blob([content], { type: mime });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    a.remove();
    URL.revokeObjectURL(url);
}

const cell = (value: string | number | null | undefined) => String(value ?? '');

/** CSV con BOM para que Excel respete tildes y eñes. */
export function exportCsv<T>(rows: T[], columns: ExportColumn<T>[], filename: string) {
    const quote = (v: string) => `"${v.replace(/"/g, '""')}"`;
    const lines = [
        columns.map((c) => quote(c.label)).join(','),
        ...rows.map((r) => columns.map((c) => quote(cell(c.value(r)))).join(',')),
    ];
    download('﻿' + lines.join('\r\n'), `${filename}.csv`, 'text/csv;charset=utf-8');
}

/** Tabla HTML con extensión .xls: Excel la abre directamente como planilla. */
export function exportExcel<T>(rows: T[], columns: ExportColumn<T>[], filename: string) {
    const head = columns.map((c) => `<th>${escapeHtml(c.label)}</th>`).join('');
    const body = rows
        .map((r) => `<tr>${columns.map((c) => `<td>${escapeHtml(cell(c.value(r)))}</td>`).join('')}</tr>`)
        .join('');
    const html = `<html xmlns:x="urn:schemas-microsoft-com:office:excel"><head><meta charset="utf-8"></head><body><table border="1"><thead><tr>${head}</tr></thead><tbody>${body}</tbody></table></body></html>`;
    download(html, `${filename}.xls`, 'application/vnd.ms-excel;charset=utf-8');
}

export interface ReportMember {
    id: number;
    enrollment_number: string;
    name: string;
    email: string;
    phone: string | null;
    dni: string;
    address: string;
    city: string;
    province: string;
    status: string;
    registration_date: string;
    province_id: number;
    city_id: number;
    account_status_id: number;
}

export const memberColumns: Record<string, ExportColumn<ReportMember>> = {
    enrollment: { key: 'enrollment', label: 'N° Matrícula', value: (m) => m.enrollment_number },
    name: { key: 'name', label: 'Nombre', value: (m) => m.name },
    dni: { key: 'dni', label: 'DNI', value: (m) => m.dni },
    email: { key: 'email', label: 'Email', value: (m) => m.email },
    phone: { key: 'phone', label: 'Teléfono', value: (m) => m.phone },
    address: { key: 'address', label: 'Dirección', value: (m) => m.address },
    city: { key: 'city', label: 'Ciudad', value: (m) => m.city },
    province: { key: 'province', label: 'Provincia', value: (m) => m.province },
    status: { key: 'status', label: 'Estado', value: (m) => m.status },
    registration_date: { key: 'registration_date', label: 'Fecha de registro', value: (m) => m.registration_date },
};
