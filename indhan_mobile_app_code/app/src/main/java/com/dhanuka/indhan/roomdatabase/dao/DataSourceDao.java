package com.dhanuka.indhan.roomdatabase.dao;

import androidx.room.Dao;
import androidx.room.Insert;
import androidx.room.Query;

import com.dhanuka.indhan.Activity.NewTicketMVP.Model.DataSourceBean;

import java.util.List;

@Dao
public interface DataSourceDao {

    @Insert
    void insert(List<DataSourceBean> data);

    @Query("SELECT * FROM DataSourceBean")
    List<DataSourceBean> getAllDataSourceList();

    @Query("SELECT COUNT(dsID) FROM DataSourceBean")
    int getNumberOfRows();

    @Query("DELETE FROM DataSourceBean")
    void deleteTableData();


    @Query("SELECT * FROM DataSourceBean WHERE item =:dataSourceName")
    DataSourceBean getDataSourceIdFromName(String dataSourceName);
}