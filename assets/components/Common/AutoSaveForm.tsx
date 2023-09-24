import React from "react";
// import { FormSpy } from "react-final-form";
import isEqual from "lodash/isEqual";
import { updateSearchValues } from "../../services/search";

interface Props {
  debounce: number;
  values: any;
  onSave: (values: any) => Promise<void>;
}

interface State {
  values: any;
  submitting: boolean;
}

class AutoSaveForm extends React.Component<Props, State> {
  timeout: NodeJS.Timeout | undefined;
  promise: Promise<any> | undefined;
  constructor(props: Props) {
    super(props);
    this.state = { values: props.values, submitting: false };
  }

  componentDidMount() {
    const { values } = this.props;
    if (!!Object.values(values).length) {
      this.props.onSave(values);
    }
  }

  UNSAFE_componentWillReceiveProps() {
    if (this.timeout) {
      clearTimeout(this.timeout);
    }
    this.timeout = setTimeout(this.save, this.props.debounce);
  }

  save = async () => {
    if (this.promise) {
      await this.promise;
    }
    const { values, onSave } = this.props;

    // This diff step is totally optional
    if (!isEqual(this.state.values, values)) {
      // values have changed
      const filledValues = {};
      Object.entries(values).forEach(([key, value]) => {
        if (value !== "" && value !== null) filledValues[key] = value;
      });
      updateSearchValues(filledValues, "filters");
      this.setState({ submitting: true, values });
      this.promise = onSave(values);
      await this.promise;
      delete this.promise;
      this.setState({ submitting: false });
    }
  };

  render() {
    return (
      this.state.submitting && <div className="submitting">Submitting...</div>
    );
  }
}

// Make a HOC
// This is not the only way to accomplish auto-save, but it does let us:
// - Use built-in React lifecycle methods to listen for changes
// - Maintain state of when we are submitting
// - Render a message when submitting
// - Pass in debounce and save props nicely
// eslint-disable-next-line react/display-name
export default AutoSaveForm;
