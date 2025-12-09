package com.dhanuka.indhan.roomdatabase.dao;

import androidx.room.Dao;
import androidx.room.Insert;
import androidx.room.OnConflictStrategy;
import androidx.room.Query;

import com.dhanuka.indhan.Activity.misAndReports.model.ReportsListData;

import java.util.List;

@Dao
public interface MisReportsListDao {

    @Insert
    void insert(List<ReportsListData> data);

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    void insert(ReportsListData data);

    @Query("SELECT * FROM ReportsListData WHERE report_type_id =:reportTypeId AND year =:year AND month =:month AND status = 1")
    List<ReportsListData> getAllMisReportDataList(int reportTypeId, int year, int month);

    @Query("DELETE  FROM ReportsListData")
    void deleteTableData();

    @Query("SELECT * FROM ReportsListData WHERE report_type_id =:reportTypeId AND month =:month AND status = 1")
    List<ReportsListData> getAllMisMonthReportDataList(int reportTypeId, int month);

    @Query("SELECT * FROM ReportsListData WHERE report_type_id =:reportTypeId AND year =:year AND status = 1")
    List<ReportsListData> getAllMisYearReportDataList(int reportTypeId, int year);

    @Query("SELECT * FROM ReportsListData WHERE report_type_id =:reportTypeId AND status =:status")
    List<ReportsListData> getAllMisActiveReportDataList(int reportTypeId, int status);
}