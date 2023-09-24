export interface Violation {
  propertyPath: string;
  title: string;
}

export interface ConstraintViolationList {
  violations: Violation[];
}
