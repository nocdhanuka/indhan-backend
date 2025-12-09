package com.dhanuka.indhan.roomdatabase.dao;

import androidx.room.Dao;
import androidx.room.Insert;
import androidx.room.Query;

import com.dhanuka.indhan.ATPMenu.activity.ATPBean;

import java.util.List;

@Dao
public interface ATPCropDao {

    @Insert
    long insert(ATPBean.Data.Crop data);

    @Query("SELECT * FROM Crop WHERE aptId =:atpId")
    List<ATPBean.Data.Crop> getAllCropList(int atpId);


    @Query("DELETE FROM Crop")
    void deleteTableData();
}