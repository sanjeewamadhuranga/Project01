import React from "react";

interface Props {
  editLink: string;
}

const EditButton = (props: Props) => (
  <a className={"btn btn-sm table-action-button"} href={props.editLink}>
    <span className="fas fa-pencil-alt table-edit-button" />
  </a>
);

export default EditButton;
