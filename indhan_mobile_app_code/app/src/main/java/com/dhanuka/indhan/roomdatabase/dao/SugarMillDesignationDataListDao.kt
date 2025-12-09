package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillDesignationData

@Dao
interface SugarMillDesignationDataListDao {
    @Insert
    fun insert(data: SugarMillDesignationData?): Long

    @get:Query("SELECT * FROM SugarMillDesignationData")
    val allDesignations: List<SugarMillDesignationData?>?

    @Query("DELETE FROM SugarMillDesignationData")
    fun deleteTableData()
}