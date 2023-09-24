import "react-toastify/dist/ReactToastify.css";
import ReactDOM from "react-dom";
import React from "react";
import { ToastContainer } from "react-toastify";
import withIntl from "../services/intl/intlContext";

const Toast = withIntl(() => (
  <ToastContainer theme="colored" hideProgressBar={true} />
));

ReactDOM.render(<Toast />, document.getElementById("toast-container"));
