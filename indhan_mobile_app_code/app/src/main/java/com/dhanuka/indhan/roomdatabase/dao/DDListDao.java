package com.dhanuka.indhan.roomdatabase.dao;

import androidx.room.Dao;
import androidx.room.Insert;
import androidx.room.Query;

import com.dhanuka.indhan.bdoddlist.BdoDdListBean;

import java.util.List;

@Dao
public interface DDListDao {

    @Insert
    void insert(List<BdoDdListBean.Data> data);

    @Query("SELECT * FROM dd_list")
    List<BdoDdListBean.Data> getAllDDList();

    @Query("SELECT COUNT(dd_id) FROM dd_list")
    int getNumberOfRows();

    @Query("DELETE  FROM dd_list")
    void deleteTableData();
}