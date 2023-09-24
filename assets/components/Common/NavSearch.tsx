import axios, { CancelTokenSource } from "axios";
import React, { useCallback, useEffect, useRef, useState } from "react";
import { Form, FormControl, Nav } from "react-bootstrap";
import { CompanyRow } from "../../models/company";
import { TransactionRow } from "../../models/transaction";
import { AdministratorRow } from "../../models/user";
import { fetchSearchResults } from "../../services/request";
import SearchResultList from "./SearchResult";

const NavSearch = () => {
  const {
    administrators,
    transactions,
    merchants,
    search,
    textRef,
    containerRef,
    isAdminLoading,
    isTransactionLoading,
    isMerchantLoading,
    show,
    setShow,
    handleChange,
    handleClearText,
  } = useNavSearch();

  return (
    <Nav className="align-items-center d-lg-block">
      <div role="banner" ref={containerRef} className="search-box dropdown">
        <Form
          className="position-relative"
          data-bs-toggle="dropdown"
          data-bs-display="static"
        >
          <FormControl
            className="search-input fuzzy-search"
            type="search"
            id="search"
            value={search}
            ref={textRef}
            onFocus={() => setShow(true)}
            onChange={(e: React.ChangeEvent<HTMLInputElement>) => {
              !show && setShow(true);
              handleChange(e);
            }}
            placeholder="Search..."
            aria-label="Search"
            role="textbox"
          />
        </Form>

        <div
          className="btn-close-falcon-container position-absolute end-0 top-50 translate-middle shadow-none"
          data-bs-dismiss="search"
        >
          {!!search.length && (
            <div
              role="button"
              className="btn-close-falcon"
              aria-label="Close"
              onClick={handleClearText}
            ></div>
          )}
        </div>
        <div
          role="menu"
          className={`custom-dropdown-menu dropdown-menu ${
            show ? "show" : ""
          } border font-base start-0 mt-2 py-0 overflow-hidden w-100`}
        >
          <ul className="scrollbar list py-3" style={{ maxHeight: "24rem" }}>
            <SearchResultList
              search={search}
              type="transactions"
              results={transactions}
              isLoading={isTransactionLoading}
            />
            <hr className="bg-200 dark__bg-900" />
            <SearchResultList
              search={search}
              type="merchants"
              results={merchants}
              isLoading={isMerchantLoading}
            />
            <hr className="bg-200 dark__bg-900" />
            <SearchResultList
              search={search}
              type="administrators"
              results={administrators}
              isLoading={isAdminLoading}
            />
          </ul>
        </div>
      </div>
    </Nav>
  );
};

const useNavSearch = () => {
  const [search, setSearch] = useState("");
  const [show, setShow] = useState(false);
  const { results: administrators, isLoading: isAdminLoading } =
    useFetchSearch<AdministratorRow>(search, "administrator");
  const { results: transactions, isLoading: isTransactionLoading } =
    useFetchSearch<TransactionRow>(search, "transaction");
  const { results: merchants, isLoading: isMerchantLoading } =
    useFetchSearch<CompanyRow>(search, "merchant");

  const containerRef = useRef<HTMLDivElement>(null);
  const textRef = useRef<HTMLInputElement>(null);

  useEffect(() => {
    document.addEventListener("keydown", removeFocus, false);

    return () => {
      document.removeEventListener("keydown", removeFocus, false);
    };
  }, []);

  const removeFocus = useCallback((event) => {
    if (event.keyCode === 27) {
      setShow(false);
      textRef.current && textRef.current?.blur();
    }
  }, []);

  const handleChange = useCallback((e: React.ChangeEvent<HTMLInputElement>) => {
    textRef.current && textRef.current?.focus();
    setSearch(e.target.value);
  }, []);

  const handleClearText = () => {
    setSearch("");
  };

  return {
    administrators,
    transactions,
    containerRef,
    merchants,
    search,
    textRef,
    show,
    setShow,
    isAdminLoading,
    isTransactionLoading,
    isMerchantLoading,
    handleChange,
    handleClearText,
  };
};

function useFetchSearch<T>(search: string, urlSuffix: string) {
  const [isLoading, setLoading] = useState(false);
  const [results, setResults] = useState<T[]>([]);
  const token = useRef<CancelTokenSource | undefined>(undefined);

  const getResults = useCallback(async () => {
    if (!!token && !!token.current) {
      token.current?.cancel();
    }
    setLoading(true);
    token.current = axios.CancelToken.source();

    const data = await fetchSearchResults<T>(urlSuffix, search, token.current);
    const newResults = data ?? [];
    setResults(newResults.length > 3 ? newResults.slice(0, 3) : newResults);
    setLoading(false);
  }, [search, urlSuffix]);

  useEffect(() => {
    search.length >= 3 && getResults();
    search === "" && setResults([]);
  }, [search]);

  return { results, isLoading };
}

export default NavSearch;
