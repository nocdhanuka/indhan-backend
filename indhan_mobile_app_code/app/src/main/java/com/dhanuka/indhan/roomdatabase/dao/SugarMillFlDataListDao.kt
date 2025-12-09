package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillFlData

@Dao
interface SugarMillFlDataListDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: SugarMillFlData?): Long

    @get:Query("SELECT * FROM SugarMillFlData")
    val allFlListType: List<SugarMillFlData?>?

    @Query("SELECT * FROM SugarMillFlData WHERE manager_id =:managerId")
    fun getAllFlByManagerEcn(managerId: String?): List<SugarMillFlData?>?

    @Query("DELETE FROM SugarMillFlData")
    fun deleteTableData()
}