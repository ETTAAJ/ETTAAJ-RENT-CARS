📘 دليل Git عام وبسيط لأي مشروع
🔹 1. إنشاء مشروع Git
git init
git remote add origin <repo-url>
git remote -v

🔹 2. التحقق من حالة المشروع
git status
git log --oneline

🔹 3. إضافة الملفات
git add <file>   إضافة ملف
git add .        إضافة كل الملفات

🔹 4. عمل Commit
git commit -m "شرح التغييرات"

🔹 5. رفع الملفات إلى GitHub
git push -u origin <branch>


بعدها:

git push

🔹 6. جلب التحديثات
git pull origin <branch>
git fetch origin

🔹 7. الفروع (Branches)
git branch                 عرض الفروع
git branch new-branch      إنشاء فرع
git checkout new-branch    الانتقال إليه
git checkout -b feature    إنشاء + انتقال
git merge <branch>         دمج فرع
git branch -d <branch>     حذف محلي
git push origin --delete <branch>  حذف من GitHub

🔹 8. تعديل آخر Commit
git commit --amend -m "رسالة جديدة"

🔹 9. التراجع
git checkout -- <file>    تراجع عن تعديل
git reset --hard          حذف كل التغييرات
git reset --soft HEAD~1   التراجع عن آخر commit مع الاحتفاظ بالملفات

🔹 10. تخزين مؤقت (Stash)
git stash
git stash pop

🔹 11. إلغاء دمج عالق
git merge --abort

🔹 12. عرض سجل رسومي
git log --graph --oneline --all

⭐ نصائح عامة

قم بعمل commits صغيرة وواضحة

نفذ دائمًا:

git pull


قبل:

git push


استخدم .gitignore

اعمل في فروع منفصلة لكل مهمة