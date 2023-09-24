import React, { useCallback, useContext, useState } from "react";
import { Card } from "react-bootstrap";
import { FormattedMessage, useIntl } from "react-intl";
import { FlowContext } from "../../../context/Context";
import {
  SectionBase,
  Section,
  SectionDropDownTypes,
} from "../../../models/onboarding";
import AddSectionForm from "./AddSectionForm";
/** Dispath the action to update the screen values */

type SectionOption = {
  key: string;
  title: string;
  description: string;
};

const DEFAULT_SECTION_OPTIONS: SectionOption[] = [
  {
    key: SectionDropDownTypes.NEW.type,
    title: SectionDropDownTypes.NEW.name,
    description:
      "With a new section, you have the freedom to add all available screens and arrange them how you like.",
  },
  {
    key: SectionDropDownTypes.CONFIRM_IDENTITY.type,
    title: SectionDropDownTypes.CONFIRM_IDENTITY.name,
    description:
      "Add a Know Your Customer (KYC) flow to your onboarding to capture and verify the user's identity. The screens within this section are set and can not be changed.",
  },
  {
    key: SectionDropDownTypes.BANK_ACCOUNT.type,
    title: SectionDropDownTypes.BANK_ACCOUNT.name,
    description:
      "The Open banking flow allows merchants to connect to a bank account. The screens within this section type are set and can not be changed.",
  },
  {
    key: SectionDropDownTypes.USER_INFO_EXT.type,
    title: SectionDropDownTypes.USER_INFO_EXT.name,
    description:
      "Use this section to capture information on the user completing the onboarding flow.",
  },
  {
    key: SectionDropDownTypes.BUSINESS_INFO_EXT.type,
    title: SectionDropDownTypes.BUSINESS_INFO_EXT.name,
    description:
      "Use this section to capture business details and understand the company.",
  },
];

type Props = {
  baseSection?: SectionBase;
  isEdit: boolean;
};

/** Modal to Add/Edit section */
const AddSection = ({ baseSection, isEdit }: Props): JSX.Element => {
  const [initialValues, setInitialValues] = useState<SectionBase>(
    baseSection ?? {
      key: "",
    }
  );

  const {
    flowDispatch: flowDispatch,
    flowState: {
      flowModal: { step },
      sections,
    },
  } = useContext(FlowContext);
  const intl = useIntl();

  const handleSelectSectionType = useCallback(
    (section: SectionOption) => () => {
      if (isSectionUsed(section?.key)) {
        return;
      }
      setInitialValues({ ...section });
      flowDispatch({
        type: "CHANGE_STEP_FLOW_MODAL",
        payload: 2,
      });
    },
    [flowDispatch]
  );

  const isSectionUsed = useCallback((key: string): boolean => {
    const duplicates = sections.find((section: Section) => section.key === key);

    return !!duplicates;
  }, []);

  return (
    <div className="section-form">
      {step === 1 ? (
        <>
          <h3 className="fs-1">
            <FormattedMessage id="onboarding.flows.sections.selectSectionType" />
          </h3>
          <ul className="list-unstyled d-flex flex-column flex-xl-row flex-xl-wrap">
            {DEFAULT_SECTION_OPTIONS.map((item) => (
              <Card
                key={item.key}
                className={`kanban-section-type-card ${
                  isSectionUsed(item.key)
                    ? " bg-700"
                    : "kanban-section-type-card-hover"
                }`}
                onClick={handleSelectSectionType(item)}
              >
                <div className="h-100 p-3 d-flex align-items-center">
                  <div>
                    <p className="mb-0 fw-medium font-sans-serif stretched-link fs--1">
                      {item.title}
                    </p>
                    <p className="mt-0 mb-2 fs--2">{item.description}</p>
                  </div>
                  <i className="fas fa-chevron-right mx-2"></i>
                </div>
              </Card>
            ))}
          </ul>
        </>
      ) : (
        <AddSectionForm
          isSectionUsed={isSectionUsed}
          initialValues={initialValues}
          isEdit={isEdit}
        />
      )}
    </div>
  );
};

export default AddSection;
