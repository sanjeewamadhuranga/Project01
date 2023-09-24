import React, { Component } from "react";
import axios, { AxiosResponse } from "axios";
import { ChoiceOption, FieldProps } from "../../models/form";
import { FormGroup, FormLabel } from "react-bootstrap";
import { Field } from "formik";
import { FormattedMessage } from "react-intl";
import CustomSelect from "../Common/CustomSelect";

interface State {
  options: Array<ChoiceOption>;
}

type Props = FieldProps;

export default class SelectSubscriptionField extends Component<Props, State> {
  options: Array<ChoiceOption> = [];
  state: State = {
    options: [],
  };

  componentDidMount(): void {
    axios
      .get("/quick-search/subscriptions")
      .then((response: AxiosResponse<ChoiceOption[]>) =>
        this.setState({
          options: response.data.map((item) => {
            return { value: item.value, label: item.label };
          }),
        })
      );
  }

  render(): JSX.Element {
    return (
      <FormGroup>
        <FormLabel>
          <FormattedMessage id={this.props.label} />
        </FormLabel>
        <Field
          name={this.props.name}
          options={this.state.options}
          component={CustomSelect}
          placeholder="Select"
          isMulti={true}
        />
      </FormGroup>
    );
  }
}
