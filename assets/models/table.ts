// Column definition for table
import { CSSProperties, ReactElement } from "react";

type Values<T> = T[keyof T];

export type ColumnId<T extends DataRow> = (keyof T & string) | null;

export type Column<T extends DataRow> = Values<{
  [Prop in keyof T & string]: {
    id: Prop | null;
    name: string;
    style?: CSSProperties;
    render?: (data: T[Prop], row: T) => ReactElement | string;
    sortable?: boolean;
  };
}>;

export enum SortDirection {
  asc = "asc",
  desc = "desc",
}

export interface Sort<T extends DataRow> {
  column: ColumnId<T>;
  direction: SortDirection;
}

export type PaginationType = "cursor" | "simple" | "length_aware";

export interface ServerResponse<T extends DataRow> {
  data: Data<T>;
  draw: number;
  pagination: PaginationInfo;
}

export interface PaginationInfo {
  perPage: number;
  filteredCount?: number;
  totalCount?: number;
  type: PaginationType;
  nextCursor?: string | null;
  previousCursor?: string | null;
  nextPage?: boolean;
  previousPage?: boolean;
}

export type Columns<T extends DataRow = DataRow> = Array<Column<T>>; // Columns definitions
export type Data<T extends DataRow = DataRow> = Array<T>; // Table data from server: T is the type of a single row returned from server
export type DataRow = Record<string, ColValue>; // A single row from server
// eslint-disable-next-line @typescript-eslint/no-explicit-any
export type ColValue = any; // Single column value - it could be anything returned from server
