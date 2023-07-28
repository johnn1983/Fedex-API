export default function () {
  const toPlainText = (html: string, length: number = 350): string => html
    .replace(/<[^>]*>/g, ' ')
    .slice(0, length) + '...'

  return { toPlainText }
}
