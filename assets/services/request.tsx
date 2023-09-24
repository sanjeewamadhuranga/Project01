import React from "react";
import axios, { AxiosResponse, CancelTokenSource } from "axios";
import * as qs from "qs";
import { toast } from "react-toastify";
import { DataRow, ServerResponse, Sort } from "../models/table";
import ToastMsg from "../components/Common/ToastMsg";
import { PaginationInfo } from "../components/Table/TableContext";

interface Filters<T extends DataRow> {
  draw: number;
  sort: Sort<T>;
  search?: string | null;
  extraData: Record<string, unknown>;
  pagination: PaginationInfo;
}

export function getTableData<T extends DataRow>(
  source: string,
  filters: Filters<T>,
  cancelToken?: CancelTokenSource,
  serverSide = true
) {
  const { pagination, sort, draw } = filters;
  return axios
    .get(source, {
      params: {
        start: serverSide ? (pagination.currentPage - 1) * pagination.limit : 0,
        length: serverSide ? pagination.limit : -1,
        draw,
        sort_column: serverSide ? sort.column : null,
        sort_dir: serverSide ? sort.direction : null,
        cursor: pagination.cursor,
        search: filters.search,
        ...filters.extraData,
      },
      cancelToken: cancelToken?.token,
      paramsSerializer: (params) => qs.stringify(params),
    })
    .then(({ data }: AxiosResponse<ServerResponse<T>>) => data)
    .catch((e) => {
      if (!axios.isCancel(e)) {
        toast.error(
          <ToastMsg
            labelId="common.actions.errorLabelMsg"
            msgId="common.actions.errorDescriptionMsg"
          />
        );
      }
      return null;
    });
}

export async function fetchSearchResults<T>(
  urlSuffix: string,
  query: string,
  cancelToken?: CancelTokenSource
) {
  try {
    const data: AxiosResponse<T[]> = await axios.get(`/search/${urlSuffix}`, {
      params: { ...(!!query && { query }) },
      cancelToken: cancelToken?.token,
    });
    return data.data;
  } catch (e) {
    if (!axios.isCancel(e)) {
      toast.error(
        <ToastMsg
          labelId="common.actions.errorLabelMsg"
          msgId="common.actions.errorDescriptionMsg"
        />
      );
    }
    // throw e;
  }
}

export const Request = {
  async fetchData<T>(url: string, params?: any) {
    try {
      const data: AxiosResponse<T> = await axios.get(url, {
        params,
      });
      return data.data;
    } catch (e) {
      if (!axios.isCancel(e)) {
        toast.error(
          <ToastMsg
            labelId="common.actions.errorLabelMsg"
            msgId="common.actions.errorDescriptionMsg"
          />
        );
      }
    }
  },
};
