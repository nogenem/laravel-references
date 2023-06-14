import { format, parseISO } from 'date-fns';

export default function formatDate(date) {
    if (!date) return '';
    return format(parseISO(date), 'PP');
}
