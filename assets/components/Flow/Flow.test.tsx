import { waitFor } from "@testing-library/react";
import React from "react";
import { mockDndSpacing } from "react-beautiful-dnd-test-utils";
import Flow from "./Flow";
import { flow } from "../../dummyData/flow";
import { Request } from "../../services/request";
import { renderWithIntl } from "../../helpers/testHelpers";

const renderFlow = async () => {
  const renderedDom = renderWithIntl(<Flow flowid="testId" />);
  await waitFor(() => {
    mockDndSpacing(renderedDom?.container);
  });
  return renderedDom;
};

jest.mock("axios");

describe("<Flow />", () => {
  test("fetch api to get flow data", async () => {
    jest.spyOn(React, "useEffect").mockImplementation((f) => f());
    jest
      .spyOn(Request, "fetchData")
      .mockImplementationOnce(() => Promise.resolve(flow));
    const { getAllByText, container } = await renderFlow();

    await waitFor(() => {
      expect(Request.fetchData).toHaveBeenCalledTimes(1);
      expect(container.getElementsByClassName("spinner-grow").length).toBe(0);
    });

    const items = await getAllByText("DUMMY Genie Flow");
    expect(items).toHaveLength(1);
  });
});
