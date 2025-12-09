package com.dhanuka.indhan.roomdatabase.dao;

import androidx.room.Dao;
import androidx.room.Insert;
import androidx.room.Query;

import com.dhanuka.indhan.ATPMenu.activity.ATPBean;

import java.util.List;

@Dao
public interface ATPProductDao {

    @Insert
    long insert(ATPBean.Data.Product data);

    @Query("SELECT * FROM Product WHERE aptId =:atpId")
    List<ATPBean.Data.Product> getAllProductList(int atpId);


    @Query("DELETE FROM Product")
    void deleteTableData();
}