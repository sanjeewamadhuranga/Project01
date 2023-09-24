import { FlowState, FlowAction } from "./types";

export const flowReducer = (state: FlowState, action: FlowAction) => {
  const { type, payload } = action;

  switch (type) {
    /** Open Modal */
    case "OPEN_FLOW_MODAL":
      const {
        size,
        step,
        maxSteps,
        type: modalType,
        isSteppable,
        children,
        modalTitle,
      } = payload;
      return {
        ...state,
        flowModal: {
          ...state.flowModal,
          modalContent: {
            ...state.flowModal.modalContent,
            children,
            modalTitle,
          },
          show: true,
          isSteppable: isSteppable ?? true,
          maxSteps,
          step,
          size,
          type: modalType,
        },
      };

    case "CHANGE_STEP_FLOW_MODAL":
      return {
        ...state,
        flowModal: {
          ...state.flowModal,
          step: payload,
        },
      };

    case "TOGGLE_FLOW_MODAL":
      return {
        ...state,
        flowModal: {
          ...state.flowModal,
          show: !state.flowModal.show,
        },
      };

    case "FLOW_NOT_PUBLISHED":
      return {
        ...state,
        isPublished: false,
      };

    case "FLOW_PUBLISHED":
      return {
        ...state,
        isPublished: true,
      };

    case "UPDATE_SEARCH_INPUT":
      return {
        ...state,
        flowModal: {
          ...state.flowModal,
          addScreen: {
            ...state.flowModal.addScreen,
            searchInput: payload?.search,
          },
        },
      };

    /** Add Screen */
    case "ADD_SCREEN":
      return {
        ...state,
        sections: state.sections?.map((section) =>
          section.key === payload.sectionKey && !!section.screens
            ? {
                ...section,
                screens: [...section?.screens, payload?.newScreen],
              }
            : section
        ),
      };

    /** Update Screen */
    case "UPDATE_SCREEN":
      return {
        ...state,
        sections: state?.sections.map((section) => {
          if (section?.key === payload?.sectionKey && !!section.screens) {
            return {
              ...section,
              screens: section?.screens?.map((screen) =>
                screen?.key === payload.updatedScreen?.key
                  ? { ...payload.updatedScreen }
                  : screen
              ),
            };
          } else {
            return section;
          }
        }),
      };

    /** Remove Screen */
    case "REMOVE_SCREEN":
      return {
        ...state,
        sections: state.sections?.map((section) => {
          if (!!section?.screens) {
            const filteredScreens = section?.screens.filter(
              (screen) => screen?.key !== payload.key
            );
            section.screens = filteredScreens;
          }
          return section;
        }),
      };

    /** Select screen category */
    case "ADDSCREEN_SELECT_CATEGORY":
      return {
        ...state,
        flowModal: {
          ...state.flowModal,
          addScreen: {
            ...state.flowModal.addScreen,
            selectedCategory: payload?.categoryKey,
          },
        },
      };

    /** Add Section */
    case "ADD_SECTION":
      return {
        ...state,
        sections: [...state.sections, payload],
      };

    case "EDIT_SECTION":
      return {
        ...state,
        sections: state.sections.map((section) => {
          if (section.key === payload.key) {
            return {
              ...payload,
              screens: section.screens,
            };
          }
          return section;
        }),
      };

    /** Update Section */
    case "UPDATE_SECTION":
      return {
        ...state,
        sections: state.sections?.map((section) =>
          section?.key === payload.column.key
            ? {
                ...section,
                screens: [...payload.reorderedItems],
              }
            : section
        ),
      };
    /** Update Dual Section */
    case "UPDATE_DUAL_SECTION":
      return {
        ...state,
        sections: state.sections?.map((section) =>
          section?.key === payload.sourceColumn.key ||
          section?.key === payload.destColumn.key
            ? {
                ...section,
                screens:
                  (section?.key === payload.sourceColumn.key &&
                    payload.updatedSourceItems) ||
                  (section?.key === payload.destColumn.key &&
                    payload.updatedDestItems),
              }
            : section
        ),
      };

    /** Remove Section */
    case "REMOVE_SECTION":
      return {
        ...state,
        sections: state.sections?.filter(
          (section) => section.key !== payload.key
        ),
      };

    /** Update Sections */
    case "UPDATE_SECTIONS":
      return {
        ...state,
        sections: [...payload.reorderedItems],
      };

    /** Update Flow Info*/
    case "UPDATE_FLOW_INFO":
      return {
        ...state,
        ...payload?.updatedFlowInfo,
      };

    /** Update Flow Info*/
    case "UPDATE_FLOW":
      return {
        ...state,
        ...payload,
      };

    default:
      return state;
  }
};
