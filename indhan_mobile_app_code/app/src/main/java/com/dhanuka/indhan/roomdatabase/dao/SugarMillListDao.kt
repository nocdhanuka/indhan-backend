package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillListData

@Dao
interface SugarMillListDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: SugarMillListData?): Long

    @Query("SELECT * FROM SugarMillListData")
    fun allSugarMills(): List<SugarMillListData?>?

    @Query("SELECT * FROM SugarMillListData WHERE sugarmill_id =:id AND office_type =:officeType")
    fun allSugarMillsBranchByOfficeType(id: Int, officeType: String): List<SugarMillListData?>?

    @Query("SELECT * FROM SugarMillListData WHERE parent_sugarmill_id =:id AND office_type =:officeType")
    fun allSugarMillsBranchByOfficeTypeAndSugarMillType(
        id: Int,
        officeType: String
    ): List<SugarMillListData?>?

    @Query("SELECT COUNT(parent_sugarmill_id) FROM SugarMillListData WHERE parent_sugarmill_id =:id AND office_type =:officeType")
    fun getBranchCount(
        id: Int,
        officeType: String
    ): Int

    @Query("SELECT * FROM SugarMillListData WHERE office_type=:isHeadOffice")
    fun allSugarMillsCompany(isHeadOffice: String): List<SugarMillListData?>?

    @Query("DELETE FROM SugarMillListData")
    fun deleteTableData()
}