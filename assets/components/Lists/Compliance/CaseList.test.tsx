import React from "react";
import CaseList, { CaseRow, Props } from "./CaseList";
import { CaseStatus } from "./CaseStatusLabel";
import MockAdapter from "axios-mock-adapter";
import axios from "axios";
import times from "lodash/times";
import { act, fireEvent, waitFor, within } from "@testing-library/react";
import { renderWithIntl } from "../../../helpers/testHelpers";
import settings from "../../../services/settings";
import text from "../../../../assets/i18n/en.json";
import { format } from "date-fns";

const renderComponent = (props: Props) =>
  renderWithIntl(<CaseList {...props} />);

settings.layout = {
  condensed: true,
  fluid: true,
};

describe("<CaseList /> component", () => {
  let mockedAxios;
  beforeAll(() => {
    mockedAxios = new MockAdapter(axios);
  });

  afterEach(() => {
    mockedAxios.reset();
  });

  test("should display case table list with loaded data from api", async () => {
    const responseData: CaseRow[] = times(5, () => fake.compliance.caseRow());
    mockedAxios
      .onGet("/compliance/case/list")
      .reply(200, { data: responseData });
    const { getByText, getAllByText } = renderComponent({
      extraData: {},
    });

    await waitFor(() => {
      responseData.forEach((row) => {
        expect(getAllByText(row.id)).toHaveLength(1);
        expect(getByText(row.company.name)).toBeInTheDocument();
        expect(getByText(row.company.name)).toHaveAttribute(
          "href",
          `/merchants/${row.company.id}`
        );
      });
    });
  });

  describe("display correct texts in approval status", () => {
    test("with in approval status", async () => {
      const responseData: CaseRow = {
        ...fake.compliance.caseRow(),
        status: CaseStatus.in_approval,
        approved: true,
        reviewed: false,
      };
      mockedAxios
        .onGet("/compliance/case/list")
        .reply(200, { data: [responseData] });
      const { getByText, queryByRole, getByRole } = renderComponent({
        extraData: {},
      });

      await waitFor(() => {
        expect(getByText("In approval")).toBeInTheDocument();
      });

      await act(async () => {
        fireEvent.click(getByText("In approval"));
      });

      const modal = getByRole("dialog");
      await waitFor(() => {
        expect(modal).toBeInTheDocument();
      });

      const { getByText: getByTextInModal } = within(modal);
      expect(
        getByRole("heading", {
          name: text.compliance.case.status.createdOn,
          level: 5,
        })
      ).toBeInTheDocument();
      if (responseData.createdAt) {
        expect(
          getByTextInModal(
            `${format(
              new Date(responseData.createdAt),
              "dd/MM/yyyy HH:mm:ss"
            )} GMT`
          )
        ).toBeInTheDocument();
      }
      expect(
        getByRole("heading", {
          name: text.compliance.case.status.assignedReview,
          level: 5,
        })
      ).toBeInTheDocument();
      expect(
        getByTextInModal(text.compliance.case.status.notHandlerAssigned)
      ).toBeInTheDocument();
      expect(
        getByRole("heading", {
          name: text.compliance.case.status.assignedApproval,
          level: 5,
        })
      ).toBeInTheDocument();
      expect(
        getByRole("heading", {
          name: text.compliance.case.status.outcome,
          level: 5,
        })
      ).toBeInTheDocument();

      await act(async () => {
        fireEvent.click(modal);
      });

      await waitFor(() => {
        expect(queryByRole("dialog")).toBeNull();
      });
    });

    test("with open status", async () => {
      const responseData: CaseRow[] = [
        {
          ...fake.compliance.caseRow(),
          status: CaseStatus.open,
          approved: false,
          reviewed: false,
          approver: null,
        },
      ];
      mockedAxios
        .onGet("/compliance/case/list")
        .reply(200, { data: responseData });
      const { getByText } = renderComponent({
        extraData: {},
      });

      await waitFor(() => {
        expect(getByText("Open")).toBeInTheDocument();
      });
    });
  });

  test("display correct texts in closed status", async () => {
    const responseData: CaseRow[] = [
      {
        ...fake.compliance.caseRow(),
        status: CaseStatus.closed,
        handler: "handler",
        reviewed: true,
      },
    ];
    mockedAxios
      .onGet("/compliance/case/list")
      .reply(200, { data: responseData });
    const { getByText } = renderComponent({
      extraData: {},
    });

    await waitFor(() => {
      expect(getByText("Closed")).toBeInTheDocument();
    });
  });
});
