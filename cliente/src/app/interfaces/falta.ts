import { User } from "./user";

export interface Falta {
    email: string;
    fecha: string;
    hora: string;
    user?: User;
    isEditing?: boolean
}