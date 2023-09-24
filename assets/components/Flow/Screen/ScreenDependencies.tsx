import { Field } from "formik";
import React, { useEffect, useState } from "react";
import { FormCheck } from "react-bootstrap";
import {
  getBusinessType,
  getReactSelectCompatibleList,
} from "../../../utils/arrays";
import CustomSelect from "../../Common/CustomSelect";
import _ from "lodash";
import { FormattedMessage } from "react-intl";
import { businessTypes, company } from "../../../constants/general";
import { Dependency } from "../../../reducers/types";

const intlPrefix = "onboarding.flows.screens";

const businessTypesList = [
  {
    key: company.BUSINESS_TYPE_UK.toString(),
    types: businessTypes.company.BUSINESS_TYPE_UK,
  },
  {
    key: company.BUSINESS_TYPE_LK.toString(),
    types: businessTypes.company.BUSINESS_TYPE_LK,
  },
];

interface Props {
  onSwitchChange: (isSwitchOn: boolean) => void;
  dependencies?: Array<Dependency>;
}

const ScreenDependencies = ({ onSwitchChange, dependencies }: Props) => {
  const [selectedKey, setSelectedKey] = useState<string>();
  const [selectedTypes, setSelectedTypes] = useState<Array<string>>([]);
  const [isSwitchOn, setIsSwitchOn] = useState(false);

  const handleSelectedKeyChange = (option) => {
    setSelectedTypes([]);
    setSelectedKey(option?.value);
  };

  const handleSelectedTypesChange = (options: []) => {
    setSelectedTypes(options);
  };

  const handleSwitchChange = () => {
    setIsSwitchOn(!isSwitchOn);
    onSwitchChange(!isSwitchOn);
  };

  useEffect(() => {
    const _dependencies = dependencies ?? [];
    setSelectedKey(getBusinessType(_dependencies));
    if (_dependencies?.length > 0) {
      setIsSwitchOn(true);
    }
  }, [dependencies]);

  return (
    <>
      <div className="mb-3">
        <div className="d-flex align-items-center">
          <h5 className="screen-dependecies-switch m-0 pe-3">
            <FormattedMessage id={`${intlPrefix}.dependencySwitchLabel`} />
          </h5>
          <FormCheck
            data-testid="screen-dependencies-switch"
            checked={isSwitchOn}
            className="m-0 fw-bold"
            id="screen-dependencies-switch"
            type="switch"
            onChange={handleSwitchChange}
          />
        </div>

        <div className="screen-dependecies-switch-helpertext fw-normal">
          <FormattedMessage id={`${intlPrefix}.dependencySwitchHelperText`} />
        </div>
      </div>
      {isSwitchOn && (
        <div className="screen-dependecies-options">
          <div className="fw-bold mb-3">
            <FormattedMessage
              id={`${intlPrefix}.dependencyOptionsHelperText1`}
            />
          </div>
          <div className="d-flex border border-1 rounded p-3 align-items-center bg-white">
            <div className="fw-bold me-3">
              <FormattedMessage
                id={`${intlPrefix}.dependencyOptionsHelperText2`}
              />
            </div>
            <Field
              className="w-25 mx-2"
              name="businessTypeKey"
              options={getReactSelectCompatibleList(
                businessTypesList.map((item) => item.key)
              )}
              component={CustomSelect}
              placeholder="Select"
              isMulti={false}
              onChange={handleSelectedKeyChange}
              dataTestId="businesstype-key-select"
            />
            <Field
              isDisabled={!selectedKey}
              className="flex-fill mx-2"
              values={selectedTypes}
              name="businessTypes"
              options={
                !!selectedKey &&
                getReactSelectCompatibleList(
                  _.find(businessTypesList, { key: selectedKey })?.types ?? []
                )
              }
              component={CustomSelect}
              isMulti={true}
              onChange={handleSelectedTypesChange}
              dataTestId="businesstypes-select"
            />
          </div>
        </div>
      )}
    </>
  );
};

export default ScreenDependencies;
