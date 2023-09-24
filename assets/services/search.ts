import * as qs from "qs";

type SearchType = "all" | "filters" | "list_settings";

type ObjectType<T> = T extends "all"
  ? Record<string, unknown>
  : Record<string, unknown> | object;

export const getSearchValues = <S extends SearchType>(
  searchType: S
): ObjectType<S> => {
  const searchStr = window.location.search;
  if (!searchStr) {
    clearSearchValues();
    return {};
  }

  const splittedSearchString = searchStr.includes("?")
    ? searchStr.slice(1)
    : searchStr;

  const searchedValues = qs.parse(splittedSearchString, {
    plainObjects: true,
  });
  if (searchType === "all") {
    return searchedValues;
  }

  return (searchedValues[searchType as string] ?? {}) as ObjectType<S>;
};

export const updateSearchValues = <T>(values: T, key: SearchType) => {
  if (!Object.values(values as object).length) {
    clearSearchValues();
    return;
  }

  const searchedValues = getSearchValues("all");
  searchedValues[key] = values;
  window.history.pushState("", "", "?" + qs.stringify(searchedValues));
};

export const clearSearchValues = () => {
  window.history.pushState("", "", window.location.pathname);
};

export const getLocalStorageData = <T>(itemName: string): T | null => {
  const data = localStorage.getItem(itemName);
  if (data !== null) {
    return JSON.parse(data);
  }
  return null;
};
