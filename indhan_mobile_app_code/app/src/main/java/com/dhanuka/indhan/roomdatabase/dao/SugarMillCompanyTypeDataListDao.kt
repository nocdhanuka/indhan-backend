package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillCompanyData

@Dao
interface SugarMillCompanyTypeDataListDao {
    @Insert
    fun insert(data: SugarMillCompanyData?): Long

    @get:Query("SELECT * FROM SugarMillCompanyData")
    val allCompanyType: List<SugarMillCompanyData?>?

    @Query("DELETE FROM SugarMillCompanyData")
    fun deleteTableData()
}