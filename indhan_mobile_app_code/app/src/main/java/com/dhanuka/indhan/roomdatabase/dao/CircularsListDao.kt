package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.CircularListData

@Dao
interface CircularsListDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: CircularListData?): Long

    @Query("SELECT * FROM CircularListData")
    fun allCircularsList(): List<CircularListData?>?

    @Query("DELETE FROM CircularListData")
    fun deleteTableData()
}