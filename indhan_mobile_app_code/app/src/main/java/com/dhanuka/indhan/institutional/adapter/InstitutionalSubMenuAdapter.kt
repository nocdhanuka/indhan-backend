package com.dhanuka.indhan.institutional.adapter

import android.app.Activity
import android.content.Context
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.R
import com.dhanuka.indhan.institutional.model.InstitutionalSubMenuModel
import kotlinx.android.synthetic.main.layout_menu_item.view.*

class InstitutionalSubMenuAdapter(
    val context: Context,
    val subMenuList: List<InstitutionalSubMenuModel>,
    private val mActivityContext: Activity
) : RecyclerView.Adapter<InstitutionalSubMenuAdapter.ViewHolder>() {
    private lateinit var clickListener: SubMenuClickListener

    inner class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
            view.setOnClickListener { view ->
                if ((subMenuList != null) && (subMenuList.size > absoluteAdapterPosition) && (absoluteAdapterPosition != -1)) {
                    clickListener.onClick(view, subMenuList[absoluteAdapterPosition])
                }
            }
        }
    }

    fun setClickListener(itemClickListener: SubMenuClickListener) {
        this.clickListener = itemClickListener
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val inflater =
            LayoutInflater.from(parent.context).inflate(R.layout.layout_menu_item, parent, false)
        return ViewHolder(inflater)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        val catName = holder.itemView.tvMeCategoryName
        holder.itemView.IvImage.setImageDrawable(subMenuList[holder.absoluteAdapterPosition].iconImage)
        catName.text = subMenuList[holder.absoluteAdapterPosition].appMenuName
    }

    override fun getItemCount(): Int {
        return subMenuList.size
    }

    interface SubMenuClickListener {
        fun onClick(view: View, subMenu: InstitutionalSubMenuModel)
    }


}