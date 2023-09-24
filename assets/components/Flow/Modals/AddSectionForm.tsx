import { Form, Formik, FormikHelpers, FormikProps } from "formik";
import React, { useContext, useRef, useState } from "react";
import { useIntl } from "react-intl";
import { toast } from "react-toastify";
import * as Yup from "yup";
import { DEFAULT_LOCALE, LocaleType } from "../../../constants/general";
import { FlowContext } from "../../../context/Context";
import { getLocalesList, mapRules } from "../../../helpers/utils";
import { Section, SectionBase, SectionType } from "../../../models/onboarding";
import { isPresetKey } from "../../../utils/flowUtils";
import CustomTooltip from "../../Common/CustomTooltip";
import InputField from "../../Fields/InputField";
import TextAreaField from "../../Fields/TextAreaField";
import ScreenTranslations from "../Screen/ScreenTranslations";

const intlPrefix = "onboarding.flows.sections";

const SCHEMA = Yup.object().shape({
  titleTranslations: Yup.lazy((map) =>
    Yup.object(mapRules(map, Yup.string().required("Required")))
  ),
  descriptionTranslations: Yup.lazy((map) =>
    Yup.object(mapRules(map, Yup.string()))
  ),
  key: Yup.string().required(),
  position: Yup.string(),
  icon: Yup.string(),
});

type Props = {
  initialValues?: SectionBase;
  isSectionUsed: (key: string) => boolean;
  isEdit: boolean;
};

function AddSectionForm({ isSectionUsed, initialValues, isEdit }: Props) {
  const ref = useRef<FormikProps<any>>(null);
  const [selectedLocaleCode, setSelectedLocaleCode] = useState(DEFAULT_LOCALE);

  const intl = useIntl();
  const {
    flowState: { locales },
    flowDispatch: flowDispatch,
  } = useContext(FlowContext);

  const onSubmit = (
    values: SectionBase,
    formikHelpers: FormikHelpers<SectionBase>
  ) => {
    if (!isEdit && isSectionUsed(values.key)) {
      formikHelpers.setFieldError("key", "The Key value has been used.");
      return;
    }

    const payload = {
      ...values,
      title: values?.titleTranslations?.en || "",
      description: values?.descriptionTranslations?.en || "",
    };

    payload["screens"] = [];

    if (
      values.key === SectionType.CONFIRM_IDENTITY ||
      values.key === SectionType.BANK_ACCOUNT
    ) {
      payload["screens"] = undefined;
    }

    if (isEdit) {
      flowDispatch({
        type: "EDIT_SECTION",
        payload: {
          ...values,
          title: values?.titleTranslations?.en || "",
          description: values?.descriptionTranslations?.en || "",
        },
      });
    } else {
      flowDispatch({
        type: "ADD_SECTION",
        payload: payload as Section,
      });
    }

    flowDispatch({ type: "FLOW_NOT_PUBLISHED" });
    flowDispatch({
      type: "TOGGLE_FLOW_MODAL",
    });
    toast.success(intl.formatMessage({ id: "common.successCreated" }));
  };

  const getFormInitialValues = () => {
    const formTitleTranslations: Record<string, string> = {};
    const formDescriptionTranslations: Record<string, string> = {};

    if (locales) {
      for (const locale of [...locales, "en"]) {
        formTitleTranslations[locale] =
          (initialValues?.titleTranslations &&
            initialValues?.titleTranslations[locale]) ??
          initialValues?.titleTranslations?.[DEFAULT_LOCALE] ??
          (initialValues?.title || "");

        formDescriptionTranslations[locale] =
          (initialValues?.descriptionTranslations &&
            initialValues?.descriptionTranslations[locale]) ??
          initialValues?.descriptionTranslations?.[DEFAULT_LOCALE] ??
          (initialValues?.description || "");
      }
    }

    return {
      titleTranslations: formTitleTranslations,
      descriptionTranslations: formDescriptionTranslations,
      key: initialValues?.key ?? "",
      position: initialValues?.position ?? 0,
      icon: initialValues?.icon ?? "",
    };
  };

  return (
    <Formik
      innerRef={ref}
      initialValues={getFormInitialValues()}
      onSubmit={onSubmit}
      validateOnBlur={false}
      validateOnChange={false}
      validationSchema={SCHEMA}
    >
      {() => (
        <Form>
          <InputField
            name="key"
            type="text"
            label={`${intlPrefix}.key`}
            placeHolder={`${intlPrefix}.key`}
            isDisabled={isPresetKey(initialValues?.key ?? "")}
          />
          {/* <InputField
            name="position"
            type="text"
            label={`${intlPrefix}.position`}
            placeHolder={`${intlPrefix}.position`}
            validateOnBlur={false}
          /> */}
          {/* <InputField
            name="icon"
            type="text"
            label={`${intlPrefix}.icon`}
            placeHolder={`${intlPrefix}.icon`}
            validateOnBlur={false}
          /> */}
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
          <TextAreaField
            name={`descriptionTranslations.${selectedLocaleCode}`}
            type="text"
            label={`${intlPrefix}.description`}
            placeHolder={`${intlPrefix}.description`}
            validateOnBlur={false}
          />
          <div className="d-flex justify-content-end">
            <InputField
              name="submit"
              type="submit"
              label={
                !isEdit ? "common.actions.submit" : "common.actions.update"
              }
            />
          </div>
        </Form>
      )}
    </Formik>
  );
}

export default AddSectionForm;
