import React from "react";
import { act, fireEvent, waitFor } from "@testing-library/react";
import ServerTable from "./ServerTable";
import Chance from "chance";

const chance = new Chance();

import times from "lodash/times";
import { SortDirection } from "../../models/table";
import NoResult from "./NoResult";
import axios from "axios";
import settings from "../../services/settings";
import { renderWithIntl } from "../../helpers/testHelpers";

const getData = (num: number) =>
  times(num, () => ({
    countryName: chance.country({ full: true }),
    countryCode: chance.country(),
    dialingCode: "+65",
    enabled: chance.bool(),
    created: chance.date(),
    id: chance.guid(),
  }));

const routePrefix = "/configuration/country";

const response = {
  draw: 0,
  data: getData(3),
  pagination: {
    perPage: 1,
    nextPage: false,
    previousPage: false,
    type: "simple",
  },
};

settings.layout = {
  fluid: false,
  condensed: true,
};

const renderComponent = (props) =>
  renderWithIntl(
    <ServerTable
      source={`${routePrefix}/list`}
      searchableFields={["countryName", "countryCode", "dialingCode"]}
      columns={[
        {
          id: "countryName",
          name: "Name",
          render: (name, country): JSX.Element => (
            <a href={`${routePrefix}/${country.id}`}>{name}</a>
          ),
        },
        { id: "countryCode", name: "Country Code" },
        { id: "flag", name: "Flag" },
        { id: "dialingCode", name: "Dialing Prefix" },
      ]}
      sort={{ column: "countryCode", direction: SortDirection.asc }}
      noResult={
        <NoResult
          header="No Configuration for country"
          body="Country added will appear here"
        />
      }
      {...props}
    />
  );

describe("<ServerTable /> component", () => {
  let axiosSpy: jest.SpyInstance;

  afterEach(() => {
    axiosSpy?.mockRestore();
  });

  describe("render serverSide table", () => {
    test("render table with loaded data", async () => {
      axiosSpy = jest.spyOn(axios, "get").mockResolvedValue({ data: response });

      const { getAllByRole } = renderComponent({ serverSide: true });

      await waitFor(() =>
        expect(axiosSpy).toHaveBeenCalledWith(
          `${routePrefix}/list`,
          expect.objectContaining({
            params: {
              cursor: null,
              draw: 1,
              length: 25,
              search: undefined,
              sort_column: "countryCode",
              sort_dir: "asc",
              start: 0,
            },
          })
        )
      );

      expect(getAllByRole("columnheader")).toHaveLength(4);
    });
  });

  describe("simulate user interactions in serverSide", () => {
    test("test changing per page limit", async () => {
      axiosSpy = jest
        .spyOn(axios, "get")
        .mockResolvedValue({ data: { ...response, draw: 1 } });

      const { getByText, getByTestId } = renderComponent({
        serverSide: true,
      });

      // first load
      await waitFor(() =>
        expect(axiosSpy).toHaveBeenCalledWith(
          `${routePrefix}/list`,
          expect.objectContaining({
            params: {
              cursor: null,
              draw: 1,
              length: 25,
              search: undefined,
              sort_column: "countryCode",
              sort_dir: "asc",
              start: 0,
            },
          })
        )
      );

      expect(getByText("Show")).toBeInTheDocument();
      const select = getByTestId("perpage-dropdown");
      expect(select).toBeInTheDocument();
      fireEvent.change(select, { target: { value: "10" } });
      await waitFor(() =>
        expect(getByTestId("perpage-dropdown")).toHaveValue("10")
      );
      // reload after chaning pagination limit
      await waitFor(() =>
        expect(axiosSpy).toHaveBeenCalledWith(
          `${routePrefix}/list`,
          expect.objectContaining({
            params: {
              cursor: null,
              draw: 2,
              length: 10,
              search: undefined,
              sort_column: "countryCode",
              sort_dir: "asc",
              start: 0,
            },
          })
        )
      );
    });

    test("test simple pagination, click next page", async () => {
      axiosSpy = jest.spyOn(axios, "get").mockResolvedValue({
        data: {
          ...response,
          pagination: { ...response.pagination, nextPage: true },
          draw: 1,
        },
      });

      const { getByRole, getAllByRole } = renderComponent({
        serverSide: true,
      });

      await waitFor(() =>
        expect(axiosSpy).toHaveBeenCalledWith(
          `${routePrefix}/list`,
          expect.objectContaining({
            params: {
              cursor: null,
              draw: 1,
              length: 25,
              search: undefined,
              sort_column: "countryCode",
              sort_dir: "asc",
              start: 0,
            },
          })
        )
      );

      const btnNext = getByRole("button", { name: "Next" });
      expect(btnNext).not.toBeDisabled();
      expect(getAllByRole("listitem")[0].children[0]).toHaveAttribute(
        "disabled",
        ""
      );
      fireEvent.click(btnNext);

      //reload
      await waitFor(() => expect(axiosSpy).toHaveBeenCalledTimes(2));
    });

    test("test cursor pagination, click next page", async () => {
      axiosSpy = jest.spyOn(axios, "get").mockResolvedValue({
        data: {
          ...response,
          pagination: {
            ...response.pagination,
            type: "cursor",
            nextCursor: "",
          },
          draw: 1,
        },
      });

      const { getByRole } = renderComponent({
        serverSide: true,
      });

      await waitFor(() => expect(axiosSpy).toHaveBeenCalled());

      const btnNext = getByRole("button", { name: "Next" });
      expect(btnNext).not.toBeDisabled();
      fireEvent.click(btnNext);
    });

    test("test sorting by header column", async () => {
      axiosSpy = jest.spyOn(axios, "get").mockResolvedValue({
        data: {
          ...response,
          pagination: {
            ...response.pagination,
            type: "cursor",
            nextCursor: "",
          },
          draw: 1,
        },
      });

      const { getByRole, getAllByTestId } = renderComponent({
        serverSide: true,
      });

      await waitFor(() =>
        expect(axiosSpy).toHaveBeenCalledWith(
          `${routePrefix}/list`,
          expect.objectContaining({
            params: {
              cursor: null,
              draw: 1,
              length: 25,
              search: undefined,
              sort_column: "countryCode",
              sort_dir: "asc",
              start: 0,
            },
          })
        )
      );

      expect(getByRole("columnheader", { name: "Name" })).toBeInTheDocument();
      const triggers = getAllByTestId("sort-trigger");
      expect(triggers[0]).toBeInTheDocument();
      fireEvent.click(triggers[0]);

      await waitFor(() =>
        expect(axiosSpy).toHaveBeenCalledWith(
          `${routePrefix}/list`,
          expect.objectContaining({
            params: {
              cursor: null,
              draw: 2,
              length: 25,
              search: undefined,
              sort_column: "countryName",
              sort_dir: "asc",
              start: 0,
            },
          })
        )
      );
    });

    test("test changing page in clientSide", async () => {
      axiosSpy = jest.spyOn(axios, "get").mockResolvedValue({
        data: {
          ...response,
          pagination: {
            ...response.pagination,
            nextPage: true,
          },
          data: getData(26),
          draw: 1,
        },
      });

      const { getByText, getByRole, getAllByRole, container } = renderComponent(
        {
          serverSide: false,
        }
      );

      await waitFor(() => {
        act(() => {
          expect(axiosSpy).toHaveBeenCalledWith(
            `${routePrefix}/list`,
            expect.objectContaining({
              params: {
                cursor: null,
                draw: 1,
                length: -1,
                search: undefined,
                sort_column: null,
                sort_dir: null,
                start: 0,
              },
            })
          );
        });
        expect(
          container.getElementsByClassName("placeholder-glow").length
        ).toBe(0);
      });

      expect(getByText("Jump to page:")).toBeInTheDocument();
      expect(getByRole("textbox")).toHaveAttribute("max", "2");
      expect(getByRole("textbox")).toHaveValue(1);

      const btnNext = getByRole("button", { name: "Next" });
      expect(btnNext).not.toBeDisabled();
      act(() => {
        fireEvent.click(btnNext);
      });
      expect(getAllByRole("listitem")[3]).toHaveClass("disabled");
      expect(getAllByRole("listitem")[3].children[0]).toHaveAttribute(
        "disabled",
        ""
      );
      await waitFor(() =>
        expect(getByRole("textbox")).toHaveAttribute("value", "2")
      );
      expect(axiosSpy).toHaveBeenCalledTimes(1);
    });

    test("test filter page in clientSide", async () => {
      const data = getData(2);
      axiosSpy = jest.spyOn(axios, "get").mockResolvedValue({
        data: {
          ...response,
          pagination: {
            ...response.pagination,
            nextPage: true,
          },
          data,
          draw: 1,
        },
      });

      const { getByText, container } = renderComponent({
        serverSide: false,
        search: data[0].countryName,
      });

      await waitFor(() => {
        expect(axiosSpy).toHaveBeenCalledWith(
          `${routePrefix}/list`,
          expect.objectContaining({
            params: {
              cursor: null,
              draw: 1,
              length: -1,
              search: data[0].countryName,
              sort_column: null,
              sort_dir: null,
              start: 0,
            },
          })
        );

        expect(
          container.getElementsByClassName("placeholder-glow").length
        ).toBe(0);
      });

      const { countryName, countryCode, dialingCode } = data[0];
      expect(getByText(countryName)).toBeInTheDocument();
      expect(getByText(countryCode)).toBeInTheDocument();
      expect(getByText(dialingCode)).toBeInTheDocument();
    });
  });
});
