export interface PaginatedResponse<T> {
  items: Array<T>;
  pagination: PaginationData;
}

export interface PaginationData {
  current_page: number;
  has_previous_page: boolean;
  has_next_page: boolean;
  per_page: number;
  total_items: number;
  total_pages: number;
}

export interface HasLoadingState {
  isLoading: boolean;
}

export interface Entity {
  id: string;
  createdAt: string | null;
  updatedAt: string | null;
  deleted: boolean;
}

export interface Address {
  address1: string | null;
  address2: string | null;
  city: string | null;
  country: string | null;
  postalCode: string | null;
  state: string | null;
}

export interface Admin extends Entity {
  email: string;
}
