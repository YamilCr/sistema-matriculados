import { toPng } from 'html-to-image';
import { jsPDF } from 'jspdf';

export async function exportToPdf(element: HTMLElement | null, filename: string = 'documento.pdf') {
    if (!element) {
        console.error('Elemento no encontrado para imprimir');
        return;
    }

    try {
        const options = {
            quality: 1,
            pixelRatio: 4,
            backgroundColor: undefined, 

            filter: (node: HTMLElement) => {
                if (node.classList && node.classList.contains('hide-on-print')) {
                    return false;
                }
                return true;
            },

            // CORRECCIÓN AQUÍ: Agregamos el tipo ': Document'
            onclone: (clonedDoc: Document) => {
                // Buscamos el contenedor en el clon
                const carnetContainer = clonedDoc.querySelector('.printable-card-container') as HTMLElement;
                
                if (carnetContainer) {
                    // Quitamos sombras y bordes en la copia para evitar las esquinas negras
                    carnetContainer.style.boxShadow = 'none';
                    carnetContainer.style.border = 'none';
                    carnetContainer.style.overflow = 'hidden';
                }
            }
        };

        const dataUrl = await toPng(element, options);

        const pdf = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4'
        });

        const imgProps = pdf.getImageProperties(dataUrl);
        const pdfWidth = pdf.internal.pageSize.getWidth();
        
        const margin = 15;
        const finalWidth = pdfWidth - (margin * 2);
        const finalHeight = (imgProps.height * finalWidth) / imgProps.width;

        const cornerRadius = 8; 

        pdf.roundedRect(margin, margin, finalWidth, finalHeight, cornerRadius, cornerRadius, 'F');
        pdf.clip();
        
        pdf.addImage(dataUrl, 'PNG', margin, margin, finalWidth, finalHeight);
        
        pdf.save(filename);

    } catch (error) {
        console.error('Error al generar el PDF:', error);
    }
}