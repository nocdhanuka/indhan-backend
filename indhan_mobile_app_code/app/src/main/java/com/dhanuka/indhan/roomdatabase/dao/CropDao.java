package com.dhanuka.indhan.roomdatabase.dao;

import androidx.room.Dao;
import androidx.room.Insert;
import androidx.room.OnConflictStrategy;
import androidx.room.Query;

import com.dhanuka.indhan.MenuScreenActivity.dbmodel.CropBean;

import java.util.List;

@Dao
public interface CropDao {

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    void insert(CropBean data);

    @Query("SELECT * FROM CropBean")
    List<CropBean> getAllCropList();

    @Query("DELETE FROM CropBean")
    void deleteTableData();


    @Query("SELECT * FROM CropBean WHERE crop =:cropName")
    CropBean getCropIdFromName(String cropName);
}