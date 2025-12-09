package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.teaEstate.TeasEstateListData

@Dao
interface TeaEstateListDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: TeasEstateListData?): Long

    @Query("SELECT * FROM TeasEstateListData")
    fun allTeaEstates(): List<TeasEstateListData?>?

    @Query("SELECT * FROM TeasEstateListData WHERE tea_estate_id =:id AND office_type =:officeType")
    fun allTeaEstatesBranchByOfficeType(id: Int, officeType: String): List<TeasEstateListData?>?

    @Query("SELECT * FROM TeasEstateListData WHERE parent_tea_estate_id =:id AND office_type =:officeType")
    fun allTeaEstatesBranchByOfficeTypeAndTeaEstateType(
        id: Int,
        officeType: String
    ): List<TeasEstateListData?>?

    @Query("SELECT COUNT(parent_tea_estate_id) FROM TeasEstateListData WHERE parent_tea_estate_id =:id AND office_type =:officeType")
    fun getBranchCount(
        id: Int,
        officeType: String
    ): Int

    @Query("SELECT * FROM TeasEstateListData WHERE office_type=:isHeadOffice")
    fun allTeaEstatesCompany(isHeadOffice: String): List<TeasEstateListData?>?

    @Query("DELETE FROM TeasEstateListData")
    fun deleteTableData()
}