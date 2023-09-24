import React, { useContext, useState } from "react";
import { Badge, Button, FormCheck } from "react-bootstrap";
import { FlowContext } from "../../../context/Context";
import { toast } from "react-toastify";
import { FormattedMessage, useIntl } from "react-intl";
import { Formik, Form, Field } from "formik";
import {
  DEFAULT_LOCALE,
  LocaleType,
  SUPPORTED_LOCALE_CODES,
} from "../../../constants/general";
import InputField from "../../Fields/InputField";
import CustomSelect from "../../Common/CustomSelect";
import * as Yup from "yup";
import { getLocaleDisplayName, getLocalesList } from "../../../helpers/utils";

const SCHEMA = Yup.object().shape({
  name: Yup.string().required("Required"),
  key: Yup.string().required("Required"),
  isDefault: Yup.boolean(),
  locales: Yup.array().of(Yup.string()),
});

const intlPrefix = "onboarding.flows.header.edit";
interface Props {
  flowKey: string;
  name: string;
  isDefault?: boolean;
  locales?: Array<string>;
}

/** Modal to Edit screen */
const EditFlow = ({ flowKey, name, isDefault, locales }: Props) => {
  const { flowDispatch: flowDispatch } = useContext(FlowContext);

  const intl = useIntl();

  const [isChecked, setIsChecked] = useState(isDefault);
  const [selectedLanguages, setSelectedLanguages] = useState<Array<string>>([]);

  const handleClose = () => {
    flowDispatch({ type: "TOGGLE_FLOW_MODAL" });
  };

  const handleSubmit = (updatedFlow) => {
    flowDispatch({
      type: "UPDATE_FLOW_INFO",
      payload: {
        updatedFlowInfo: {
          key: updatedFlow.key,
          name: updatedFlow.name,
          default: isChecked,
          primaryLanguage: DEFAULT_LOCALE,
          locales: updatedFlow.locales,
        },
      },
    });

    flowDispatch({ type: "FLOW_NOT_PUBLISHED" });
    flowDispatch({
      type: "TOGGLE_FLOW_MODAL",
    });
    toast.success(intl.formatMessage({ id: "common.successCreated" }));
  };

  const handleIsDefaultCheck = () => {
    setIsChecked(!isChecked);
  };

  const getLanguagesList = (array: Array<LocaleType>) => {
    return array
      .map((item) => ({
        value: item.code,
        label: item.name,
      }))
      .filter((item) => item.value !== "en");
  };

  const handleLanguagesChange = (options: []) => {
    setSelectedLanguages(options);
  };

  return (
    <Formik
      initialValues={{
        name: name ?? "",
        key: flowKey ?? "",
        isDefault: isDefault ?? false,
        locales: locales ?? [],
      }}
      onSubmit={handleSubmit}
      validationSchema={SCHEMA}
    >
      {({ touched }) => (
        <Form className="m-3">
          <InputField
            name="name"
            type="text"
            label={`${intlPrefix}.name`}
            placeHolder={"common.fields.enter"}
            validateOnBlur={false}
          />
          <InputField
            name="key"
            type="text"
            label={`${intlPrefix}.key`}
            placeHolder={"common.fields.enter"}
            validateOnBlur={false}
          />

          <div className="mb-3">
            <div className="d-flex align-items-center">
              <h5 className="screen-dependecies-switch m-0 pe-3 pb-2">
                <FormattedMessage id={`${intlPrefix}.languagesTitle`} />
              </h5>
            </div>
            <div className="screen-dependecies-switch-helpertext fw-normal mb-3 text-650">
              <FormattedMessage id={`${intlPrefix}.languagesHelperText`} />
            </div>
            <div className="mb-3">
              <h5 className="kanban-flow-edit-primary m-0 pe-3 pb-2 text-650">
                <FormattedMessage id={`${intlPrefix}.primarylanguage`} />
              </h5>
              <Badge
                className="badge-soft-info text-wrap w-fit-content me-3"
                bg=""
              >
                {getLocaleDisplayName(DEFAULT_LOCALE, intl)}
              </Badge>
            </div>

            <div className="mb-3">
              <h5 className="kanban-flow-edit-additional-languages m-0 pe-3 pb-2 text-650">
                <FormattedMessage id={`${intlPrefix}.additionalLanguages`} />
              </h5>
              <Field
                className="flex-fill"
                values={selectedLanguages}
                name="locales"
                options={getLanguagesList(
                  getLocalesList(
                    window?.APP_SETTINGS?.enabledLanguages ??
                      SUPPORTED_LOCALE_CODES,
                    intl
                  ) as Array<LocaleType>
                )}
                component={CustomSelect}
                isMulti={true}
                onChange={handleLanguagesChange}
                dataTestId="additional-languages-select"
              />
            </div>
          </div>

          <div className="d-flex align-items-center">
            <h5 className="screen-dependecies-switch m-0 pe-3">
              <FormattedMessage id={`${intlPrefix}.isDefault`} />
            </h5>
            <FormCheck
              data-testid="screen-dependencies-switch"
              checked={isChecked}
              className="m-0 fw-bold"
              id="screen-dependencies-switch"
              type="switch"
              onChange={handleIsDefaultCheck}
            />
          </div>

          <div className="screen-dependecies-switch-helpertext fw-normal">
            <FormattedMessage id={`${intlPrefix}.isDefaultHelpText`} />
          </div>

          <div className="d-flex justify-content-end">
            <span className="modal-footer-buttons">
              <Button
                size="sm"
                className="w-100 mt-4 action-download-button"
                onClick={handleClose}
                variant="light"
              >
                <FormattedMessage id="common.actions.cancel" />
              </Button>
              <InputField
                name="submit"
                type="submit"
                label="common.actions.save"
                validateOnBlur={false}
              />
            </span>
          </div>
        </Form>
      )}
    </Formik>
  );
};

export default EditFlow;
