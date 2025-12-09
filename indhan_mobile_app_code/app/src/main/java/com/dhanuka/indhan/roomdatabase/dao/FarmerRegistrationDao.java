package com.dhanuka.indhan.roomdatabase.dao;

import androidx.room.Dao;
import androidx.room.Insert;
import androidx.room.OnConflictStrategy;
import androidx.room.Query;

import com.dhanuka.indhan.RegisterFarmerScreen.normalFarmerRegistration.FarmerRegistrationBean;

import java.util.List;

@Dao
public interface FarmerRegistrationDao {

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    void insert(FarmerRegistrationBean data);

    @Query("SELECT * FROM FarmerRegistrationBean WHERE ticketNumber =:ticketNumber AND isDeleted =:value")
    List<FarmerRegistrationBean> getAllNumberList(String ticketNumber, int value);

    @Query("DELETE FROM FarmerRegistrationBean")
    void deleteTableData();

    @Query("UPDATE FarmerRegistrationBean SET isDeleted =:value WHERE ticketNumber =:ticketNumber AND mobileNumber =:contactNumber")
    void contactNumberDelete(String ticketNumber, String contactNumber, int value);
}