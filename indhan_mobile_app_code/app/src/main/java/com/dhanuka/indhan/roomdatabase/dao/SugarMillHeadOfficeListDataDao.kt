package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillHeadOfficeData

@Dao
interface SugarMillHeadOfficeListDataDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: SugarMillHeadOfficeData?): Long

    @get:Query("SELECT * FROM SugarMillHeadOfficeData")
    val allSugarMill: List<SugarMillHeadOfficeData?>?

    @Query("DELETE FROM SugarMillHeadOfficeData")
    fun deleteTableData()
}