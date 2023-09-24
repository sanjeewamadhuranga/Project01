import React from "react";
import { within } from "@testing-library/react";
import times from "lodash/times";
import Body from "./Body";
import BoolLabel from "./BoolLabel";
import { renderWithIntl } from "../../helpers/testHelpers";
import DateFormatter from "../Common/DateFormatter";
import Chance from "chance";

const chance = new Chance();

const routePrefix = "/configuration/country";

const getData = (num: number) =>
  times(num, () => ({
    countryName: chance.country({ full: true }),
    countryCode: chance.country(),
    dialingCode: "+65",
    enabled: chance.bool(),
    created: chance.date({ max: new Date() }).toString(),
    id: chance.guid().toString(),
  }));

type ColumnsType = {
  countryName: string;
  countryCode: string;
  dialingCode: string;
  enabled: boolean;
  created: string;
};

const columns = [
  {
    id: "countryName",
    name: "Name",
    render: (name, country): JSX.Element => (
      <a href={`${routePrefix}/${country.id}`}>{name}</a>
    ),
  },
  { id: "countryCode", name: "Country Code" },
  { id: "dialingCode", name: "Dialing Prefix" },
  {
    id: "enabled",
    name: "Enabled",
    render: (enabled: boolean): JSX.Element => <BoolLabel value={enabled} />,
  },
  {
    id: "created",
    name: "Created",
    render: (created: string) => <DateFormatter date={created} />,
  },
];

describe("<Body /> component", () => {
  test("test loading table", () => {
    const { getAllByRole } = renderWithIntl(
      <table>
        <Body<ColumnsType>
          columns={columns}
          data={getData(0)}
          isLoading={true}
        />
      </table>
    );
    // check number of rows
    expect(getAllByRole("row")).toHaveLength(10);

    // check number of columns
    getAllByRole("row").forEach((item) => {
      const { getAllByRole } = within(item);
      expect(getAllByRole("cell")).toHaveLength(5);
      getAllByRole("cell").forEach((cell) => {
        expect(cell).toHaveClass("placeholder-glow");
      });
    });
  });

  describe("test displaying loaded table", () => {
    test("test empty table, with no data", () => {
      const { getByRole } = renderWithIntl(
        <table>
          <Body columns={columns} data={getData(0)} isLoading={false} />
        </table>
      );
      expect(getByRole("cell")).toHaveAttribute(
        "colspan",
        columns.length.toString()
      );
    });

    test("test table filled with data", () => {
      const propsData = getData(3);
      const { getAllByRole } = renderWithIntl(
        <table>
          <Body columns={columns} data={propsData} isLoading={false} />
        </table>
      );

      const rows = getAllByRole("row");
      expect(rows).toHaveLength(3);
      rows.forEach((row, index) => {
        const rowData = propsData[index];
        const { getByRole, getByText } = within(row);
        expect(
          getByRole("link", { name: rowData.countryName })
        ).toHaveAttribute("href", `${routePrefix}/${rowData.id}`);

        expect(getByRole("cell", { name: rowData.countryCode }));
        expect(getByRole("cell", { name: rowData.dialingCode }));
        expect(getByText(rowData.enabled ? "Yes" : "No")).toHaveClass(
          `badge-soft-${
            rowData.enabled ? "success" : "danger"
          } badge rounded-pill`
        );
      });
    });
  });
});
