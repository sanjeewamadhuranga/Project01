import React, { useContext, useRef, useState } from "react";
import { FlowContext } from "../../../context/Context";
import { useIntl } from "react-intl";
import { toast } from "react-toastify";
import ScreenDependencies from "../Screen/ScreenDependencies";
import { Formik, FormikProps, Form } from "formik";
import * as Yup from "yup";
import InputField from "../../Fields/InputField";
import {
  getBusinessType,
  getSelectedBusinessTypes,
} from "../../../utils/arrays";
import { Dependency } from "../../../reducers/types";
import {
  isValidDependencies,
  prepareNewScreenPayload,
} from "../../../utils/flowUtils";
import ScreenTranslations from "../Screen/ScreenTranslations";
import { DEFAULT_LOCALE, LocaleType } from "../../../constants/general";
import {
  getLocalesList,
  getOnboardingFlowScreenIntlDefaults,
  mapRules,
} from "../../../helpers/utils";
import CustomTooltip from "../../Common/CustomTooltip";

const intlPrefix = "onboarding.flows.screens";

const SCHEMA = Yup.object().shape({
  titleTranslations: Yup.lazy((map) =>
    Yup.object(mapRules(map, Yup.string().required("Required")))
  ),
  descriptionTranslations: Yup.lazy((map) =>
    Yup.object(mapRules(map, Yup.string()))
  ),
  key: Yup.string().required("Required"),
  dependencies: Yup.array().of(
    Yup.object().shape({
      field: Yup.string(),
      value: Yup.string(),
      comparison: Yup.string(),
    })
  ),
});

type Props = {
  sectionKey: string;
  screenKey: string;
  title?: string;
  description?: string | JSX.Element;
  dependencies: Array<Dependency>;
  titleTranslations: Record<string, string>;
  descriptionTranslations: Record<string, string>;
};

type NewScreenData = {
  businessTypeKey: string;
  businessTypes: Array<string>;
  key: string;
  titleTranslations: Record<string, string>;
  descriptionTranslations: Record<string, string>;
};

/** Modal to Edit screen */
const EditScreen = ({
  screenKey,
  sectionKey,
  title,
  description,
  dependencies,
  titleTranslations,
  descriptionTranslations,
}: Props) => {
  const {
    flowState: { locales },
    flowDispatch: flowDispatch,
  } = useContext(FlowContext);
  const ref = useRef<FormikProps<NewScreenData>>(null);
  const [selectedLocaleCode, setSelectedLocaleCode] = useState(DEFAULT_LOCALE);

  const hasDependencies = dependencies[0]?.value?.length > 0;

  /** User switch the dependencies */
  const [isSwitchOn, setIsSwitchOn] = useState(hasDependencies);
  const intl = useIntl();

  const handleSubmit = (newScreenData: NewScreenData) => {
    const updatedScreen = prepareNewScreenPayload(
      { ...newScreenData, title: title || "", description: description || "" },
      isSwitchOn
    );
    const isEmpty = !Object.keys(updatedScreen).length;

    if (!isEmpty) {
      flowDispatch({
        type: "UPDATE_SCREEN",
        payload: { updatedScreen, sectionKey },
      });
      flowDispatch({ type: "FLOW_NOT_PUBLISHED" });
      flowDispatch({
        type: "TOGGLE_FLOW_MODAL",
      });
      toast.success(intl.formatMessage({ id: "common.successUpdated" }));
    }
  };

  const onSwitchChange = (switched) => {
    setIsSwitchOn(switched);
  };

  const getFormInitialValues = () => {
    const formTitleTranslations: Record<string, string> = {};
    const formDescriptionTranslations: Record<string, string> = {};

    if (locales) {
      for (const locale of [...locales, "en"]) {
        const intlDefaults = getOnboardingFlowScreenIntlDefaults({
          locale,
          key: screenKey,
        });

        formTitleTranslations[locale] =
          titleTranslations[locale] ??
          (intlDefaults?.title || null) ??
          titleTranslations["en"] ??
          title;

        formDescriptionTranslations[locale] =
          descriptionTranslations[locale] ??
          (intlDefaults?.description || null) ??
          descriptionTranslations["en"] ??
          description;
      }
    }

    return {
      titleTranslations: formTitleTranslations,
      descriptionTranslations: formDescriptionTranslations,
      key: screenKey ?? "",
      businessTypeKey: getBusinessType(dependencies) ?? "",
      businessTypes: getSelectedBusinessTypes(dependencies) ?? [],
    };
  };

  return (
    <Formik
      innerRef={ref}
      initialValues={getFormInitialValues()}
      validationSchema={SCHEMA}
      onSubmit={handleSubmit}
    >
      {({ touched, isValid, ...props }) => {
        return (
          <div className="bg-light rounded-top-lg px-4 py-3">
            <Form>
              <InputField
                isDisabled={true}
                name="key"
                type="text"
                label={`${intlPrefix}.key`}
                placeHolder={`${intlPrefix}.key`}
                validateOnBlur={false}
              />
              <div className="d-flex align-items-center m-3 ms-0">
                <ScreenTranslations
                  locales={
                    getLocalesList(
                      [...(locales ?? []), DEFAULT_LOCALE],
                      intl
                    ) as Array<LocaleType>
                  }
                  selectedLocaleCode={selectedLocaleCode}
                  setSelectedLocaleCode={setSelectedLocaleCode}
                />
                <CustomTooltip
                  tooltip={`${intlPrefix}.translations.tooltip`}
                  placement="bottom"
                />
              </div>
              <InputField
                name={`titleTranslations.${selectedLocaleCode}`}
                type="text"
                label={`${intlPrefix}.title`}
                placeHolder={`${intlPrefix}.title`}
                validateOnBlur={false}
              />
              <InputField
                name={`descriptionTranslations.${selectedLocaleCode}`}
                type="text"
                label={`${intlPrefix}.description`}
                placeHolder={`${intlPrefix}.description`}
                validateOnBlur={false}
              />

              {/* Update dependencies for the screen */}
              <hr className="dash-line" />
              <ScreenDependencies
                dependencies={dependencies}
                onSwitchChange={onSwitchChange}
              />

              <div className="d-flex justify-content-end mt-3">
                <InputField
                  name="submit"
                  type="submit"
                  label={`${intlPrefix}.update`}
                  isDisabled={
                    !isValid || !isValidDependencies(props, isSwitchOn)
                  }
                  validateOnBlur={false}
                />
              </div>
            </Form>
          </div>
        );
      }}
    </Formik>
  );
};

export default EditScreen;
