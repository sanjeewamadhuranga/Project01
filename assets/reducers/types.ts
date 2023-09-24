import { ReactNode } from "react";
import { Section, Screen, SectionBase } from "../models/onboarding";

export type FlowInfoType = {
  key: string;
  name: string;
  default?: boolean;
  isPublished?: boolean;
  locales?: Array<string>;
  primaryLanguage?: string;
};

export type UpdatedFlowInfo = { updatedFlowInfo: FlowInfoType };

export type AddScreenSearchInputType = { search: string };

export type RemoveSectionType = { key: string };

export type UpdateSectionsType = { reorderedItems: Array<Section> };

export type RemoveScreenType = { key: string };

export type UpdatedScreenType = { updatedScreen: Screen; sectionKey: string };

export type NewScreenType = { newScreen: Screen; sectionKey: string };

export type FlowModalType =
  | "addScreen"
  | "addSection"
  | "editFlow"
  | "editScreen";

export type FlowState = {
  key: string;
  name: string;
  id?: string;
  default?: boolean;
  isPublished?: boolean;
  locales?: Array<string>;
  readonly flowModal: {
    modalContent: {
      modalTitle: string;
      children?: ReactNode;
    };
    type?: FlowModalType;
    show: boolean;
    step?: number;
    maxSteps?: number;
    isSteppable?: boolean;
    size?: "sm" | "lg" | "xl";
    addScreen: {
      selectedCategory: string | undefined;
      searchInput?: string;
    };
  };
  readonly sections: Array<Section>;
};

export type FlowAction =
  | {
      type: "OPEN_FLOW_MODAL";
      payload:
        | {
            size?: "sm" | "lg" | "xl";
            step?: number;
            maxSteps?: number;
            isSteppable?: boolean;
            type?: FlowModalType;
            children?: ReactNode | JSX.Element;
            modalTitle?: string;
          }
        | any;
    }
  | { type: "ADDSCREEN_SELECT_CATEGORY"; payload: { categoryKey?: string } }
  | { type: "TOGGLE_FLOW_MODAL"; payload?: null }
  | { type: "CHANGE_STEP_FLOW_MODAL"; payload: number }
  | { type: "ADD_SCREEN"; payload: NewScreenType }
  | { type: "UPDATE_SCREEN"; payload: UpdatedScreenType }
  | { type: "FLOW_NOT_PUBLISHED"; payload?: null }
  | { type: "FLOW_PUBLISHED"; payload?: null }
  | { type: "REMOVE_SCREEN"; payload: RemoveScreenType }
  | { type: "ADD_SECTION"; payload: Section }
  | { type: "EDIT_SECTION"; payload: SectionBase }
  | { type: "UPDATE_SECTION"; payload: any }
  | { type: "UPDATE_DUAL_SECTION"; payload: any }
  | { type: "REMOVE_SECTION"; payload: RemoveSectionType }
  | { type: "UPDATE_SECTIONS"; payload: UpdateSectionsType }
  | { type: "UPDATE_FLOW_INFO"; payload: UpdatedFlowInfo }
  | { type: "UPDATE_SEARCH_INPUT"; payload: AddScreenSearchInputType }
  | { type: "UPDATE_FLOW"; payload: FlowState | any };

export interface AddScreenType {
  title: string;
  key: string;
  description: string;
  tenant_keys?: Array<string>;
  countries?: Array<string>;
}

export interface AddScreenListType {
  category: string;
  category_key: string;
  screens: Array<AddScreenType>;
  countries?: Array<string>;
}

export interface Dependency {
  field: string;
  comparison: string;
  value: Array<string>;
}

export type ScreenTranslation = {
  titleTranslations: Record<string, string>;
  descriptionTranslations: Record<string, string>;
};
