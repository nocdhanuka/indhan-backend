package com.dhanuka.indhan.roomdatabase.dao;

import androidx.room.Dao;
import androidx.room.Insert;
import androidx.room.OnConflictStrategy;
import androidx.room.Query;

import com.dhanuka.indhan.addfarmers.model.AddFarmerModel;

import java.util.List;

@Dao
public interface AddFarmerDao {

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    void insert(AddFarmerModel data);

    @Query("SELECT * FROM AddFarmerModel")
    List<AddFarmerModel> getAllFarmerList();

    @Query("DELETE FROM AddFarmerModel WHERE _id =:id")
    void deleteTableData(int id);
}