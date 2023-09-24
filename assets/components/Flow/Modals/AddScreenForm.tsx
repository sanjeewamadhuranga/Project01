import { Form, Formik, FormikProps, FormikValues } from "formik";

import React, { useContext, useEffect, useRef, useState } from "react";
import { useIntl } from "react-intl";
import { toast } from "react-toastify";
import * as Yup from "yup";
import { FlowContext } from "../../../context/Context";
import { Screen } from "../../../models/onboarding";
import {
  isValidDependencies,
  prepareNewScreenPayload,
} from "../../../utils/flowUtils";
import InputField from "../../Fields/InputField";
import ScreenDependencies from "../Screen/ScreenDependencies";
import ScreenTranslations from "../Screen/ScreenTranslations";
import { DEFAULT_LOCALE, LocaleType } from "../../../constants/general";
import {
  getLocalesList,
  getOnboardingFlowScreenIntlDefaults,
  mapRules,
} from "../../../helpers/utils";
import CustomTooltip from "../../Common/CustomTooltip";

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

const intlPrefix = "onboarding.flows.screens";

interface Props {
  isScreenUsed: boolean;
  selectedScreenTemplate: Screen;
  sectionKey: string;
}

const AddScreenForm = ({
  isScreenUsed,
  selectedScreenTemplate,
  sectionKey,
}: Props) => {
  const {
    flowState: { locales },
    flowDispatch,
  } = useContext(FlowContext);
  const intl = useIntl();
  const ref = useRef<FormikProps<FormikValues>>(null);
  const [selectedLocaleCode, setSelectedLocaleCode] = useState(DEFAULT_LOCALE);

  /** Doesn't mark a screen as dependent on business type */
  const [isDepsRequired, setIsDepsRequired] = useState(true);

  /** User switch the dependencies */
  const [isSwitchOn, setIsSwitchOn] = useState(false);

  const onSwitchChange = (switched) => {
    setIsSwitchOn(switched);
  };

  const handleSubmit = (newScreen, formikHelpers) => {
    if (isScreenUsed) {
      formikHelpers.setFieldError("key", "The Key value has been used.");
      toast.warning(
        intl.formatMessage({ id: "onboarding.flows.screens.duplicateKey" })
      );
      return;
    }
    flowDispatch({
      type: "ADD_SCREEN",
      payload: {
        newScreen: prepareNewScreenPayload(newScreen, isSwitchOn),
        sectionKey,
      },
    });
    flowDispatch({ type: "FLOW_NOT_PUBLISHED" });
    flowDispatch({
      type: "TOGGLE_FLOW_MODAL",
    });
    toast.success(intl.formatMessage({ id: "common.successCreated" }));
  };

  // can't set on form initialization as `selectedScreenTemplate` will only be
  // set after
  useEffect(() => {
    if (locales) {
      const formTitleTranslations: Record<string, string> = {};
      const formDescriptionTranslations: Record<string, string> = {};

      for (const locale of [...locales, "en"]) {
        const defaults = getOnboardingFlowScreenIntlDefaults({
          locale,
          key: selectedScreenTemplate.key,
        });
        formTitleTranslations[locale] = defaults?.title || "";
        formDescriptionTranslations[locale] = defaults?.description || "";
      }
    }
  }, [locales, selectedScreenTemplate]);

  return (
    <Formik
      innerRef={ref}
      initialValues={{
        titleTranslations: {},
        descriptionTranslations: {},
        key:
          selectedScreenTemplate?.key === "NEW"
            ? ""
            : selectedScreenTemplate?.key,
      }}
      validationSchema={SCHEMA}
      onSubmit={handleSubmit}
    >
      {({ touched, isValid, ...props }) => {
        return (
          <Form className="p-4 background-light">
            <div className="mb-3">
              <label className="text-uppercase" htmlFor="key">
                Screen Details
              </label>
            </div>
            {selectedScreenTemplate?.key !== "NEW" ? (
              <div className="mb-3 text-16 clear-row">
                <div className="row">
                  <div className="col-3 text-weight-500">Key</div>
                  <div className="col-9 text-weight-400">
                    {selectedScreenTemplate?.key}
                  </div>
                </div>
                <div className="row">
                  <div className="col-3 text-weight-500">Description</div>
                  <div className="col-9 text-weight-400">
                    {selectedScreenTemplate?.description}
                  </div>
                </div>
              </div>
            ) : (
              <InputField
                name="key"
                type="text"
                label={`${intlPrefix}.key`}
                placeHolder={`${intlPrefix}.key`}
                validateOnBlur={false}
              />
            )}

            <hr className="dash-line" />
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
            {/* Adding Dependencies for the screen if it is required*/}
            {isDepsRequired && (
              <>
                <hr className="dash-line" />
                <ScreenDependencies onSwitchChange={onSwitchChange} />
              </>
            )}

            <div className="d-flex justify-content-end">
              <InputField
                name="submit"
                type="submit"
                label={`${intlPrefix}.addScreenButton`}
                isDisabled={
                  Object.values(touched).every((item) => !item) ||
                  !isValid ||
                  !isValidDependencies(props, isSwitchOn)
                }
                validateOnBlur={false}
              />
            </div>
          </Form>
        );
      }}
    </Formik>
  );
};

export default AddScreenForm;
