import { User } from "./user";

export interface Guardia {
    fecha: string;
    hora: string;
    dGuardia?: User;
    dAusente: User;
    aula: string;
    grupo: string;
    observacion: string;
}