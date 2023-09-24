import React from "react";
import { Table } from "react-bootstrap";

interface Props {
  value: unknown;
}

const NestedValue = (props: Props) => {
  if (Object(props.value) === props.value) {
    return (
      <Table className="w-auto" size="sm" striped>
        <tbody>
          {Array.isArray(props.value)
            ? props.value.map((value, key) => (
                <tr key={key}>
                  <td>
                    <NestedValue value={value} />
                  </td>
                </tr>
              ))
            : Object.keys(Object.assign({}, props.value)).map((key) => (
                <tr key={key}>
                  <th>{key}</th>
                  <td>
                    <NestedValue value={Object(props.value)[key]} />
                  </td>
                </tr>
              ))}
        </tbody>
      </Table>
    );
  }

  return (
    <span className="text-word-break">
      {(props.value as string | null) ?? "-"}
    </span>
  );
};

export default NestedValue;
