<?php

    namespace com\linways\core\util;

    use com\linways\core\exception\OnlineExamException;

    class CommonUtil
    {
        public static function prepareApiPaginationInfo($totalRecordsCount, $currentPage, $pageSize, $studentRecords)
        {
            $pagination_skip = ((int)$currentPage - 1) * (int)$pageSize;
            $hasNextPage = ($pagination_skip + 1 + $pageSize <= $totalRecordsCount);
            $recordsFrom = $pagination_skip + 1 <= $totalRecordsCount ? $pagination_skip + 1 : null;
            $recordsTo = $recordsFrom == null ? null : (int)($pagination_skip + $pageSize);
            $recordsTo = $recordsTo > $totalRecordsCount ? (int)$totalRecordsCount : $recordsTo;
            $meta = [
                "totalRecords" => $totalRecordsCount,
                "currentPage" => $currentPage,
                "hasNextPage" => $hasNextPage,
                "recordsFrom" => $recordsFrom,
                "recordsTo" => $recordsTo
            ];
            return $meta;
        }
    }